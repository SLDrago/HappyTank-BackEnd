<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopInfo extends Model
{
    use HasFactory;

    protected $table = 'shop_info';

    protected $fillable = [
        'user_id',
        'owner_name',
        'description',
        'city_id',
        'phone_number',
        'address',
        'gps_coordinates',
        'working_hours',
        'socialmedia_links'
    ];

    protected $casts = [
        'gps_coordinates' => 'array',
        'working_hours' => 'array',
        'socialmedia_links' => 'array'
    ];
}
