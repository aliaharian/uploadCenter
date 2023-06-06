<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'file_meta_id',
        'offset'
    ];

}