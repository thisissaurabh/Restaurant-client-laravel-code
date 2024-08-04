<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Addon extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "addons";
    protected $primaryKey = "id";
    protected $fillable = ['user_id', 'name', 'price', 'image'];
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
