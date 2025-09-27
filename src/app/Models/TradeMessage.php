<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'trade_id',
        'user_id',
        'message',
        'image_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }

    public function scopeUnreadByUser($query, $userId)
    {
        return $query->where('user_id', '<>', $userId)
                    ->whereNull('read_at');
    }
}