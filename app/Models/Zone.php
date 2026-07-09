<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'sort_order'];

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }
}
