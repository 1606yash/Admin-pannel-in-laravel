<?php

namespace Modules\Role\Entities;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Role extends Model
{
       
    protected $table = 'roles';
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