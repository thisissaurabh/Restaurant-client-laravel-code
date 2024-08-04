<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "coupons";
    protected $primaryKey = "id";

    protected $fillable = [
        'user_id',
        'title',
        'code',
        'limitForSameUser',
        'MinPurchase',
        'startDate',
        'expireDate',
        'discount',
        'discountType',
        'maxDiscount'
    ];
    protected $casts = [
        'startDate' => 'datetime',
        'expireDate' => 'datetime'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
