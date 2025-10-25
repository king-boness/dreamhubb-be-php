<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // názov tabuľky
    protected $table = 'posts';

    // primárny kľúč
    protected $primaryKey = 'post_id';

    // Eloquent predpokladá auto-increment integer
    public $incrementing = true;
    protected $keyType = 'int';

    // povolené polia pre mass assignment
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

    // ak chceš, aby timestamps (created_at, updated_at) fungovali
    public $timestamps = true;
}
