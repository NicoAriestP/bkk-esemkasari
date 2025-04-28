<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Model\Blameable;

class Announcement extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'title',
        'content',
        'file'
    ];
}
