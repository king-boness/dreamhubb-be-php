<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $primaryKey = 'post_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

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

public function postImages()
{
    return $this->hasMany(PostImage::class, 'post_id', 'post_id');
}
