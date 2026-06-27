<?php

namespace App\Console\Commands;

use App\Services\Import\ImportReport;
use App\Services\Import\StoryImporter;
use Illuminate\Console\Command;

class ImportStories extends Command
{
    protected $signature = 'stories:import {--path= : Override the import root directory}';

    protected $description = 'Import blog stories from author folders of Word documents described by a CSV.';

    public function handle(StoryImporter $importer): int
    {
        $root = $this->option('path') ?: config('imports.root');

        $this->info("Importing stories from: {$root}");
        $this->newLine();

        $report = $importer->import($root, function (string $type, string $message): void {
            match ($type) {
                'error' => $this->error($message),
                'warn' => $this->warn($message),
                'comment' => $this->comment($message),
                default => $this->line($message),
            };
        });

        $this->renderSummary($report);

        return self::SUCCESS;
    }

    private function renderSummary(ImportReport $report): void
    {
        $this->newLine();
        $this->info('Import summary');
        $this->table(['Metric', 'Value'], $report->toRows());

        if ($report->missingDocs > 0) {
            $logFile = config('imports.log_dir').DIRECTORY_SEPARATOR.config('imports.not_found_log');
            $this->warn("Missing documents were logged to: {$logFile}");
        }
    }
}
