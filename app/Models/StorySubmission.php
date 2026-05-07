<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'title',
    'email',
    'pdf_file',
    'status',
])]
class StorySubmission extends Model
{
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

    /** Public URL for the stored PDF, or null. */
    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_file ? Storage::url($this->pdf_file) : null;
    }

    /** Delete the stored PDF file from disk. */
    public function deletePdfFile(): void
    {
        if ($this->pdf_file) {
            Storage::delete($this->pdf_file);
        }
    }
}
