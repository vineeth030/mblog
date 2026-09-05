<?php

namespace App\Services\Import;

use App\Models\Author;
use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

/**
 * Orchestrates importing stories from the on-disk import tree into blog_posts.
 *
 * Layout: <root>/<Author Name>/stories_metadata.csv plus the Word documents it
 * references. Each row is processed defensively — one bad row never aborts the
 * run — and related inserts are wrapped in a transaction.
 */
class StoryImporter
{
    /** Progress sink: called as ($type, $message) where $type is info|comment|warn|error. */
    private $progress;

    public function __construct(private readonly WordDocumentParser $parser) {}

    public function import(?string $root = null, ?callable $progress = null): ImportReport
    {
        $root ??= config('imports.root');
        $this->progress = $progress ?? fn () => null;

        $report = new ImportReport;

        if (! File::isDirectory($root)) {
            $this->emit('error', "Import root does not exist: {$root}");

            return $report;
        }

        foreach (File::directories($root) as $authorDir) {
            $this->importAuthorFolder($authorDir, $report);
        }

        return $report;
    }

    private function importAuthorFolder(string $authorDir, ImportReport $report): void
    {
        $authorName = basename($authorDir);
        $report->authors++;
        $this->emit('info', "Processing author: {$authorName}...");

        $csvPath = $authorDir.DIRECTORY_SEPARATOR.config('imports.csv_name');

        if (! File::isFile($csvPath)) {
            $this->emit('warn', '  No '.config('imports.csv_name')." in {$authorName}, skipping folder.");

            return;
        }

        $author = $this->findOrCreateAuthor($authorName, $report);

        foreach ($this->readCsv($csvPath) as $row) {
            $report->csvRows++;
            $this->importRow($row, $authorDir, $author, $report);
        }
    }

    /**
     * @param  array<string, string>  $row
     */
    private function importRow(array $row, string $authorDir, Author $author, ImportReport $report): void
    {
        $siNow = $row['si now'] ?? '?';

        try {
            $docName = trim($row['doc name'] ?? '');
            $title = trim($row['story name'] ?? '');
            $slug = trim($row['slug'] ?? '');
            $categoryName = trim($row['category'] ?? '');

            if ($title === '' || $slug === '') {
                throw new \RuntimeException('Missing required "Story Name" or "Slug".');
            }

            // Locate the Word document; a missing file is logged and skipped.
            $docPath = $this->resolveDocPath($authorDir, $docName);

            if ($docPath === null) {
                $this->logMissingDocument($author->name, $siNow, $docName);
                $report->missingDocs++;
                $this->emit('warn', "  Skipped [SI Now={$siNow}]: document not found ({$docName}).");

                return;
            }

            // Idempotent: an existing slug means this story is already imported.
            if (BlogPost::where('slug', $slug)->exists()) {
                $report->skippedDuplicate++;
                $this->emit('comment', "  Skipped [SI Now={$siNow}]: slug already exists ({$slug}).");

                return;
            }

            $this->emit('comment', "  Importing story: {$title}...");

            // Parse the document body before opening a transaction so a parse
            // failure never leaves a half-written record behind.
            try {
                $content = $this->parser->extractText($docPath, $title);
            } catch (Throwable $e) {
                $report->parseErrors++;
                $this->emit('error', "  Skipped [SI Now={$siNow}]: cannot parse document ({$docName}): {$e->getMessage()}");

                return;
            }

            $category = $categoryName !== ''
                ? $this->findOrCreateCategory($categoryName, $report)
                : null;

            DB::transaction(function () use ($title, $slug, $category, $author, $content) {
                BlogPost::create([
                    'title' => $title,
                    'slug' => $slug,
                    'category_id' => $category?->id,
                    'author_id' => $author->id,
                    'content' => $content,
                    'publish_status' => true,
                ]);
            });

            $report->imported++;
            $this->emit('info', "  Story imported: {$title}");
        } catch (Throwable $e) {
            // Last-resort guard: report and move on, never abort the run.
            $report->rowErrors++;
            $this->emit('error', "  Failed [SI Now={$siNow}]: {$e->getMessage()}");
        }
    }

    private function findOrCreateAuthor(string $name, ImportReport $report): Author
    {
        $author = Author::firstOrCreate(['name' => $name]);

        if ($author->wasRecentlyCreated) {
            $report->authorsCreated++;
            $this->emit('comment', "  Author created: {$name}");
        }

        return $author;
    }

    private function findOrCreateCategory(string $name, ImportReport $report): Category
    {
        $category = Category::firstOrCreate(['name' => $name]);

        if ($category->wasRecentlyCreated) {
            $report->categoriesCreated++;
            $this->emit('comment', "  Category created: {$name}");
        }

        return $category;
    }

    /**
     * Resolve the "Doc Name" value to a real file inside the author folder,
     * tolerating missing extensions and casing differences between the CSV and
     * the filesystem. Returns null when nothing matches.
     */
    private function resolveDocPath(string $authorDir, string $docName): ?string
    {
        if ($docName === '') {
            return null;
        }

        // 1. Exact path as given.
        $exact = $authorDir.DIRECTORY_SEPARATOR.$docName;
        if (File::isFile($exact)) {
            return $exact;
        }

        // 2. Case-insensitive match, with or without a .doc/.docx extension.
        $targetVariants = [mb_strtolower($docName)];
        if (! preg_match('/\.docx?$/i', $docName)) {
            $targetVariants[] = mb_strtolower($docName).'.docx';
            $targetVariants[] = mb_strtolower($docName).'.doc';
        }

        foreach (File::files($authorDir) as $file) {
            if (in_array(mb_strtolower($file->getFilename()), $targetVariants, true)) {
                return $file->getPathname();
            }
        }

        return null;
    }

    /**
     * Read the CSV into rows keyed by lower-cased, trimmed header names so the
     * exact column casing in the file does not matter.
     *
     * @return array<int, array<string, string>>
     */
    private function readCsv(string $csvPath): array
    {
        $rows = [];
        $handle = fopen($csvPath, 'r');

        if ($handle === false) {
            $this->emit('error', "  Unable to open CSV: {$csvPath}");

            return $rows;
        }

        try {
            $header = fgetcsv($handle);

            if ($header === false || $header === null) {
                return $rows;
            }

            // Strip a UTF-8 BOM from the first header and normalize keys.
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
            $keys = array_map(fn ($h) => mb_strtolower(trim((string) $h)), $header);

            while (($data = fgetcsv($handle)) !== false) {
                // Skip fully blank lines.
                if ($data === [null] || $data === ['']) {
                    continue;
                }

                $row = [];
                foreach ($keys as $i => $key) {
                    $row[$key] = isset($data[$i]) ? trim((string) $data[$i]) : '';
                }
                $rows[] = $row;
            }
        } finally {
            fclose($handle);
        }

        return $rows;
    }

    private function logMissingDocument(string $author, string $siNow, string $docName): void
    {
        $logDir = config('imports.log_dir');
        File::ensureDirectoryExists($logDir);

        $logFile = $logDir.DIRECTORY_SEPARATOR.config('imports.not_found_log');

        if (! File::exists($logFile)) {
            File::put($logFile, "# Missing Story Documents\n\n");
        }

        $timestamp = Carbon::now()->toDateTimeString();
        $line = "- [{$timestamp}] author: {$author} | SI Now: {$siNow} | doc: {$docName}\n";

        File::append($logFile, $line);
    }

    private function emit(string $type, string $message): void
    {
        ($this->progress)($type, $message);
    }
}
