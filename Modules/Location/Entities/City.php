<?php

namespace Modules\Location\Entities;

use Illuminate\Database\Eloquent\Model;
use Auth;

class City extends Model
{
       
    protected $table = 'cities';
    public $timestamps = false;

    /**
     *  Setup model event hooks
     */
    /*public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->item_guid = Uuid::generate()->string;
        });
    }*/
}