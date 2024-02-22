<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
//use Uuid;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Broadcast extends Model
{
    
    //use SoftDeletes;
    protected $fillable = [];    
    protected $table = 'broadcasts';

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