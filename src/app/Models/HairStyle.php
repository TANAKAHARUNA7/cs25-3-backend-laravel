<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HairStyle extends Model
{
    protected $fillable = [
        'title',
        'image',
        'image_key',
        'description',
    ];
}
