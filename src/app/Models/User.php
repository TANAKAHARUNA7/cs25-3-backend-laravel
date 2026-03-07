<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
// リレーションの戻り値型（1対1)
use Illuminate\Database\Eloquent\Relations\HasOne;
// (1対多）
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;


// Userモデルは EloquentのModelを継承する
// → usersテーブルと自動で連動する
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    
    // 入力を許可するカラム
    protected $fillable = [
        'account',
        'password',
        'user_name',
        'role',
        'gender',
        'phone',
        'birth'
    ];

    // JSONレスポンス時に隠すカラム
    // APIでpasswordが返らないようにする
    protected $hidden = [
        'password'
    ];


    /**
     * 1対1リレーション
     * users.id → designers.user_id
     */
    public function designer(): HasOne 
    {
        return $this->hasOne(Designer::class, 'user_id');
    }

    /**
     * 1対多リレーション（client側）
     * users.id → reservations.client_id
     */
    public function reservationAsClient(): HasMany
    {
        return $this->hasMany(Reservation::class, 'client_id');
    }

}
