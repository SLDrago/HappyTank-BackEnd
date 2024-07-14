<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;

    protected $table = 'contactus';

    protected $fillable = ['type', 'first_name', 'last_name', 'email', 'message'];
}
