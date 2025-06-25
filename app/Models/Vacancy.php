<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFeaturedFile;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Vacancy extends Model
{
    use HasFactory, HasFeaturedFile;

    protected $fillable = [
        'created_by',
        'updated_by',
        'title',
        'description',
        'due_at',
        'location',
        'file'
    ];

    protected $useTypeForFileFolderName = true;
    protected $type = 'vacancy';

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

    public function createdBy()
    {
        return $this->belongsTo(Partner::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Partner::class, 'updated_by');
    }

    public function vacancyApplication()
    {
        return $this->hasMany(VacancyApplication::class);
    }
}
