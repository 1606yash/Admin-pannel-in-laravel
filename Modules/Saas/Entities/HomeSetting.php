<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Model;
//use Uuid;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class HomeSetting extends Model
{
    
    //use SoftDeletes;
    protected $fillable = [];    
    protected $table = 'home_settings';
}