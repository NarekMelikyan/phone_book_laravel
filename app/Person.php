<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'persons';
    protected $fillable = ['first_name','last_name','email'];
    public $timestamps = true;

    public function phoneNumbers(){
        return $this->hasMany(PhoneNumber::class,'person_id','id');
    }
}
