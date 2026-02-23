<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// 外部キーを持っている側が belongsTo
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Designer extends Model
{
    protected $fillable = [
        'user_id',
        'image',
        'image_key',
        'experience',
        'good_at',
        'personality',
        'message',
    ];

    // designers.user_id -> users.id（外部キーを持つ側は belongsTo）
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // designers.id -> reservations.designer_id（1対多）
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'designer_id');
    }

    // designers.id -> time_offs.designer_id（1対多）
    public function timeOffs(): HasMany
    {
        return $this->hasMany(TimeOff::class, 'designer_id');
    }

}
