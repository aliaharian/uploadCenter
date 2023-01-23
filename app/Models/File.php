<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $fillable = [
        'path',
        'name',
        'mimeType',
        'meta'
    ];
    protected $appends = ['url','relative_url'];


    public function getUrlAttribute()
    {
        return env('APP_URL') . $this->path . '/' . $this->name . '.' . $this->mimeType;
    }
    public function getRelativeUrlAttribute()
    {
        return $this->path . '/' . $this->name . '.' . $this->mimeType;
    }
}
