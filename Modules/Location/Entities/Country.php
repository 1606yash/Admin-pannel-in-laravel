<?php

namespace Modules\Location\Entities;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Country extends Model
{
       
    protected $table = 'countries';
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