<?php

namespace App\Services\Import;

use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;
use RuntimeException;
use Throwable;

/**
 * Reads a Word document (.docx, best-effort .doc) and returns its body text as
 * plain text with one block per paragraph. The leading title line — which the
 * source documents render in bold or a heading style — is dropped, since the
 * title is already known from the import metadata.
 */
class WordDocumentParser
{
    /**
     * Extract the story body from a Word document.
     *
     * @param  string  $absolutePath  Full path to the .doc/.docx file.
     * @param  string|null  $expectedTitle  CSV "Story Name", used as an extra
     *                                      signal when deciding whether the
     *                                      first line really is the title.
     *
     * @throws RuntimeException when the document cannot be parsed.
     */
    public function extractText(string $absolutePath, ?string $expectedTitle = null): string
    {
        try {
            $phpWord = IOFactory::load($absolutePath);
        } catch (Throwable $e) {
            throw new RuntimeException(
                "Unable to parse Word document [{$absolutePath}]: {$e->getMessage()}",
                previous: $e,
            );
        }

        // Flatten every section into a single ordered list of paragraph strings,
        // remembering whether each paragraph looked like a title (bold/heading).
        $paragraphs = [];

        foreach ($phpWord->getSections() as $section) {
            $this->collectParagraphs($section, $paragraphs);
        }

        // Drop any leading blank paragraphs so the title test looks at real text.
        while ($paragraphs !== [] && $paragraphs[0]['text'] === '') {
            array_shift($paragraphs);
        }

        if ($paragraphs !== [] && $this->looksLikeTitle($paragraphs[0], $expectedTitle)) {
            array_shift($paragraphs);
        }

        $text = collect($paragraphs)
            ->map(fn (array $p) => $p['text'])
            ->reject(fn (string $line, int $i) => $line === '' && $i === 0) // no leading blank
            ->implode("\n");

        return trim($text);
    }

    /**
     * Walk a container (section / textrun) and append each paragraph as
     * ['text' => string, 'bold' => bool, 'heading' => bool].
     *
     * @param  array<int, array{text: string, bold: bool, heading: bool}>  $paragraphs
     */
    private function collectParagraphs(AbstractContainer $container, array &$paragraphs): void
    {
        foreach ($container->getElements() as $element) {
            if ($element instanceof TextRun) {
                $paragraphs[] = $this->renderTextRun($element);

                continue;
            }

            if ($element instanceof Text) {
                $paragraphs[] = [
                    'text' => $this->normalize($element->getText()),
                    'bold' => $this->isBold($element),
                    'heading' => false,
                ];

                continue;
            }

            // Tables, lists and other wrappers contain their own elements;
            // recurse so nested text is not lost.
            if ($element instanceof AbstractContainer) {
                $this->collectParagraphs($element, $paragraphs);
            }
        }
    }

    /**
     * Render a paragraph made of multiple runs, joining their text and judging
     * the paragraph "bold" when the bold runs make up the bulk of its content.
     *
     * @return array{text: string, bold: bool, heading: bool}
     */
    private function renderTextRun(TextRun $run): array
    {
        $text = '';
        $boldChars = 0;
        $totalChars = 0;

        foreach ($run->getElements() as $child) {
            if (! $child instanceof Text) {
                continue;
            }

            $chunk = (string) $child->getText();
            $text .= $chunk;

            $len = mb_strlen(trim($chunk));
            $totalChars += $len;

            if ($this->isBold($child)) {
                $boldChars += $len;
            }
        }

        $paragraphStyle = $run->getParagraphStyle();
        $styleName = is_string($paragraphStyle) ? $paragraphStyle : '';

        return [
            'text' => $this->normalize($text),
            // "Mostly bold" tolerates a stray non-bold space at the end.
            'bold' => $totalChars > 0 && $boldChars >= $totalChars,
            'heading' => $this->isHeadingStyle($styleName),
        ];
    }

    /** A paragraph is treated as the title if it is bold or uses a heading style. */
    private function looksLikeTitle(array $paragraph, ?string $expectedTitle): bool
    {
        if ($paragraph['text'] === '') {
            return false;
        }

        // Strongest signal: the line matches the known story title.
        if ($expectedTitle !== null && $this->matchesTitle($paragraph['text'], $expectedTitle)) {
            return true;
        }

        return $paragraph['bold'] || $paragraph['heading'];
    }

    private function matchesTitle(string $line, string $expectedTitle): bool
    {
        $normalize = fn (string $v) => mb_strtolower(preg_replace('/\s+/u', ' ', trim($v)) ?? '');

        return $normalize($line) === $normalize($expectedTitle);
    }

    private function isBold(Text $element): bool
    {
        $font = $element->getFontStyle();

        return is_object($font) && method_exists($font, 'isBold') && (bool) $font->isBold();
    }

    private function isHeadingStyle(string $styleName): bool
    {
        return $styleName !== '' && (bool) preg_match('/^(title|heading)/i', $styleName);
    }

    private function normalize(string $text): string
    {
        // PhpWord returns text HTML-encoded (a literal " and the &quot; entity
        // both arrive as "&quot;"), so decode entities before storing or the raw
        // &quot; / &amp; / &lt; would be persisted verbatim.
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Collapse internal whitespace runs and trim; Word often emits tabs and
        // non-breaking spaces that would otherwise litter the stored content.
        $text = str_replace("\xC2\xA0", ' ', $text);

        return trim(preg_replace('/[ \t]+/u', ' ', $text) ?? '');
    }
}
