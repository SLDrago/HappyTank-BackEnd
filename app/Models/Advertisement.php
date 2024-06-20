<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $table = 'advertisements';

    protected $fillable = [
        'title',
        'small_description',
        'description',
        'price',
        'price_based_on',
        'category_id',
        'status',
        'tags',
        'views',
        'user_id'
    ];

    // Relationships
    public function images()
    {
        return $this->hasMany(AdvertisementImage::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
