<?php
namespace Modules\User\Entities;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use DB;

class OrganizationStaff extends Authenticatable implements Auditable
{
	use \OwenIt\Auditing\Auditable;
	protected $table = 'organization_staff';
	protected $dateFormat = 'Y-m-d H:i:s+';
	public $timestamps = false;
}