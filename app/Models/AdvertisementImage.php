<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertisementImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertisement_id',
        'image_url'
    ];

    // Relationships
    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
}
