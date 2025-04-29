<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Model\Blameable;
use App\Traits\HasFeaturedFile;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Announcement extends Model
{
    use HasFactory, Blameable, HasFeaturedFile;

    protected $fillable = [
        'title',
        'content',
        'file'
    ];

    protected $useTypeForFileFolderName = true;
    protected $type = 'announcement';

    protected $appends = [
        'file_url',
    ];

    /**
     * Provide web accessible image url.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function fileUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->file
                ? Storage::disk(config('filesystems.default', 'public'))->url($this->file)
                : null,
        );
    }
}
