<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fishdata extends Model
{
    public $timestamps = false;
    use HasFactory;

    protected $table = 'fishdatas';

    protected $fillable =  [
        'common_name', 'scientific_name', 'aquarium_size', 'habitat', 'max_standard_length', 'temperature', 'ph', 'diet', 'behavior', 'sexual_dimorphisms', 'reproduction', 'notes'
    ];

    public function fishImages()
    {
        return $this->hasMany(FishImage::class, 'fish_id');
    }
}
