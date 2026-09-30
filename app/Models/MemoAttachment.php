<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MemoAttachment extends Model
{
    protected $fillable = [
        'original_name',
        'storage_path',
        'mime_type',
        'size',
    ];

    public function memo(): BelongsTo
    {
        return $this->belongsTo(Memo::class);
    }

    public function imagePreview(): ?string
    {
        $disk = Storage::disk('local');
        if (!in_array($this->mime_type, ['image/png', 'image/jpeg'], true)
            || !$disk->exists($this->storage_path)) {
            return null;
        }

        $bytes = $disk->get($this->storage_path);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
        if (!in_array($mime, ['image/png', 'image/jpeg'], true)) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($bytes);
    }
}
