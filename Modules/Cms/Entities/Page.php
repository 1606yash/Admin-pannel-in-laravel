<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Page extends Model
{
    protected $fillable = [];    
    protected $table = 'pages';
}