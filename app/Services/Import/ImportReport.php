<?php

namespace App\Services\Import;

/**
 * Mutable tally of everything that happened during an import run, used to print
 * the closing summary. Kept as a plain object so the importer and command share
 * one source of truth without passing a bag of counters around.
 */
class ImportReport
{
    public int $authors = 0;

    public int $csvRows = 0;

    public int $imported = 0;

    public int $skippedDuplicate = 0;

    public int $missingDocs = 0;

    public int $parseErrors = 0;

    public int $rowErrors = 0;

    public int $categoriesCreated = 0;

    public int $authorsCreated = 0;

    public float $startedAt;

    public function __construct()
    {
        $this->startedAt = microtime(true);
    }

    /** Total stories skipped for any reason (duplicates, missing docs, errors). */
    public function totalSkipped(): int
    {
        return $this->skippedDuplicate + $this->missingDocs + $this->parseErrors + $this->rowErrors;
    }

    public function elapsedSeconds(): float
    {
        return round(microtime(true) - $this->startedAt, 2);
    }

    /**
     * @return array<int, array{0: string, 1: int|string}>
     */
    public function toRows(): array
    {
        return [
            ['Total authors', $this->authors],
            ['Total CSV rows', $this->csvRows],
            ['Stories imported', $this->imported],
            ['Stories skipped', $this->totalSkipped()],
            ['  - duplicate slug', $this->skippedDuplicate],
            ['  - missing document', $this->missingDocs],
            ['  - parse error', $this->parseErrors],
            ['  - row error', $this->rowErrors],
            ['Categories created', $this->categoriesCreated],
            ['Authors created', $this->authorsCreated],
            ['Execution time (s)', $this->elapsedSeconds()],
        ];
    }
}
