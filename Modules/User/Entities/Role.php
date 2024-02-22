<?php
namespace Modules\User\Entities;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use Spatie\Permission\Traits\HasRoles;
use DB;

class Role extends Authenticatable implements Auditable,MustVerifyEmail
{
	use HasApiTokens, Notifiable, HasRoles;
	use \OwenIt\Auditing\Auditable;
	protected $table = 'roles';
	protected $dateFormat = 'Y-m-d H:i:s+';

	protected $guard_name = 'web';
}
