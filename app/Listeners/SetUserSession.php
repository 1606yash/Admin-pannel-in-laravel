<?php

namespace app\Listeners;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Modules\User\Entities\OrganizationPermission;
use Modules\Saas\Entities\Organization;
use Modules\User\Entities\OrganizationStaff;
use Modules\User\Entities\Role;
use Modules\Saas\Entities\Settings;

class SetUserSession
{
	/**
	 * @param  Login $event
	 * @return void
	 */
	public function handle(Login $event)
	{
		$user = Auth::user();
		session(
			[
				'name'				=>"",
				'email'			 	=> "",
				'role'			  	=> "",
				'userPermission'	=>"",
				'organization_name' => "",
				'installation_type' => "",
				'organization_type' => "",
				'staff_limit' 		=>"",
				'seller_limit' 		=> "",
				'buyer_limit' 		=> "",
				'token'			 	=> "",
				'userOrganizations'	=> "",
				'currentOrganization'	=> "",
				'currentOrganizationName'	=> "",
				'tallyIntegration'	=> "",
				'ecommerceDiscount'	=> "",
				'planApprovalRequired'	=> "",
				"userDetails"		=> $user,
			]		
		);
	}
}