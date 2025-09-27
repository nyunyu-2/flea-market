<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\TradeRating;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'profile_image',
        'zipcode',
        'address',
        'building',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function products()
    {
        return $this->hasMany(Item::class);
    }

    public function purchases()
    {
        return $this->hasMany(\App\Models\Purchase::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function likedItems()
    {
        return $this->belongsToMany(Item::class, 'likes', 'user_id', 'item_id');
    }

    public function tradeMessages()
    {
        return $this->hasMany(TradeMessage::class);
    }

    public function receivedRatings()
    {
        return $this->hasMany(TradeRating::class, 'to_user_id');
    }

    public function averageRating()
    {
        // 購入者としてもらった評価
        $buyerRatings = $this->purchases()
            ->whereNotNull('buyer_rating')
            ->pluck('buyer_rating');

        // 出品者としてもらった評価
        $sellerRatings = $this->products()
            ->with('purchases')
            ->get()
            ->flatMap(fn($item) => $item->purchases->pluck('seller_rating'))
            ->filter(); // null を除外

        // 両方を合算
        $allRatings = $buyerRatings->merge($sellerRatings);

        if ($allRatings->count() === 0) {
            return null; // 評価なし
        }

        // 平均を計算し四捨五入
        return round($allRatings->avg());
    }

}