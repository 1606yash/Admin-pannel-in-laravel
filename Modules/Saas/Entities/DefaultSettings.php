<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Model;
//use Uuid;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class DefaultSettings extends Model
{
    
    //use SoftDeletes;
    protected $fillable = [];    
    protected $table = 'default_settings';

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