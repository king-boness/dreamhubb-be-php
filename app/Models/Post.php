<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // 🔹 názov tabuľky
    protected $table = 'posts';

    // 🔹 primárny kľúč
    protected $primaryKey = 'post_id';

    // 🔹 ak primárny kľúč nie je typu incrementing integer (napr. UUID), nastav:
    public $incrementing = true;

    // 🔹 ak nie je typu string
    protected $keyType = 'int';

    // 🔹 povolené polia (ktoré sa môžu hromadne vkladať)
    protected $fillable = [
        'title',
        'description',
        'user_id',
        'date_created',
        'date_deadline',
        'category_id',
        'tokens',
        'views',
    ];

    // 🔹 ak nemáš `created_at` a `updated_at` ako timestamp v DB, môžeš vypnúť timestamps:
    public $timestamps = true;
}
