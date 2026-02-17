<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salon extends Model
{
    protected $fillable = [
        'image',
        'image_key',
        'introduction',
        'information',
        'map',
        'traffic',
    ];
}
