<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishImage extends Model
{
    use HasFactory;

    protected $table = 'fishimages';

    protected $fillable = [
        'fish_id',
        'image'
    ];

    public function fish()
    {
        return $this->belongsTo(Fishdata::class, 'fish_id');
    }
}
