<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeOff extends Model
{
    protected $table = 'time_offs';

    protected $fillable = [
        'designer_id',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date'
    ];

    function designer(): BelongsTo
    {
        return $this->belongsTo(Designer::class, 'designer_id');
    }

     protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
