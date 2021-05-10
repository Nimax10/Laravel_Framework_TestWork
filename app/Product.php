<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	public $timestamps = false;

    public $fillable = [
    	'product',
    	'price',
    	'description',
    ];
}
