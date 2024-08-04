<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = "invoices";
    protected $primaryKey = "id";
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foodList()
    {
        return $this->hasMany(FoodList::class, 'invoice_id');
    }
}
