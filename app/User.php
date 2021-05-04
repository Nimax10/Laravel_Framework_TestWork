<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    public $fillable = [
        "first_name", 
        "last_name", 
        "email", 
        "login", 
        "password",
    ];

    public $hidden =
    [
    	"password",
    	"api_token",
    	"recovery_key",
    ];

}
