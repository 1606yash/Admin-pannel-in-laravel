<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Model;
//use Uuid;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class MasterCategory extends Model
{
    
    //use SoftDeletes;
    protected $fillable = [];    
    protected $table = 'master_categories';

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