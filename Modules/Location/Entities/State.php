<?php

namespace Modules\Location\Entities;

use Illuminate\Database\Eloquent\Model;
use Auth;

class State extends Model
{
       
    protected $table = 'states';
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