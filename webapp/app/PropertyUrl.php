<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PropertyUrl extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'property_urls';
    protected $primaryKey = '_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'city','url',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
