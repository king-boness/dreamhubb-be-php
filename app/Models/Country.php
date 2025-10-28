<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'continent_id'];

    public $timestamps = false;

    public function continent()
    {
        return $this->belongsTo(Continent::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
