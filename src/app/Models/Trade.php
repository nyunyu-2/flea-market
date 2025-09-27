<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function messages()
    {
        return $this->hasMany(TradeMessage::class);
    }
}
