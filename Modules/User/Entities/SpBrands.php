<?php

namespace Modules\User\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpBrands extends Model
{

    protected $table = 'sp_brands';


    protected $guarded = ['id'];
}
