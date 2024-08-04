<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;

    protected $table = "variations";
    protected $primaryKey = "id";
    protected $fillable = [
        'food_id', 'option_name', 'additional_price',
    ];

    public function food()
    {
        return $this->belongsTo(Food::class, 'food_id', 'id');
    }
}
