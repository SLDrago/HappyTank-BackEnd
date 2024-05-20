<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fishdata extends Model
{
    public $timestamps = false;
    use HasFactory;
    Protected $fillable =  [
        'Common_Name','Scientific_Name', 'Aquarium_Size','Habitat','Max_Standard_length', 'Temperature', 'PH', 'Diet', 'Behavior/Compatability', 'Sexual_Dimorphisms','Reproduction' ,'Notes'
    ];
}
