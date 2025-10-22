<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    use HasFactory;

    protected  $primaryKey = 'post_id';
    protected $fillable = [
        'title',
        'description',
        'date_created',
        'date_deadline',
        'tokens',
        'views'
    ];
    protected $hidden = [
        'category_id',
        'user_id',
    ];
    public function postImages(): HasMany
    {
        return $this->hasMany(PostImage::class, 'post_id', 'post_id');
    }
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')->select('id', 'username', 'profile_picture', 'badge_id');
    }
    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'category_id', 'category_id');
    }
}
