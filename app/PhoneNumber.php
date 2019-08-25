<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    protected $table = 'phone_numbers';
    protected $fillable = ['person_id','phone_number'];
    public $timestamps = true;
}
