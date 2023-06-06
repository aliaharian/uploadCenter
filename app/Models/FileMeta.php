<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'hashCode',
        'meta',
        'mimeType',
        'partCount',
        'size'
    ];
    protected $casts = [
        'meta' => 'array',
    ];

    function metas()
    {
        return json_decode($this->meta);
    }

    function fileParts()
    {
        return $this->hasMany(FilePart::class);
    }

}