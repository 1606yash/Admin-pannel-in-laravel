<?php

namespace Modules\User\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\User\Entities\Address;
use Illuminate\Http\Request;
use Config;
use Modules\User\Entities\State;
use Modules\User\Entities\City;
use Modules\User\Entities\District;
use Modules\User\Entities\Role;
use Modules\User\Entities\User;
use Modules\User\Entities\OrganizationBuyer;
use Modules\User\Entities\ModelRole;
use Modules\Saas\Entities\Organization;
use Uuid;
use Illuminate\Support\Facades\Auth;

class BuyerImport implements ToCollection, WithHeadingRow
{
	use Importable;

	public $data;

	public function __construct($organization_id)
	{
		$this->organization_id = $organization_id;
	}


	/**
	* @param Collection $rows
	*/
	// public function collection(Collection $rows)
	// {
	// public function collection(array $row){
	public function collection(Collection $rows){

		$errors = array();
		$success = array();
		$item = array();
		$role              =   Role::where('organization_id',$this->organization_id)->where('name',\Config::get('constants.ROLES.BUYER'))->first();
		$roleId = $role->id;
		foreach ($rows as $key => $row) 
		{
			$rowNo = $key+2;
			$importError = 0;

			$validator = Validator::make($row->toArray(), [
				'first_name' => 'required|max:75',
            	'last_name' => 'required|max:75',
				'mobile' => 'required|max:10|unique:users,phone_number',
                // 'email' => 'required|email|max:191|unique:users,email',
                /*'address' => 'required',
                'state' => 'required',
                'district' => 'required',
                'city' => 'required',
                'pincode' => 'required|numeric',*/
                'shop_name' => 'required',
			 ]);

			if ($validator->fails()) {

				foreach ($validator->errors()->toArray() as $key => $validateError) {
					$errors[] = 	array(
										'row' => $rowNo,
										'first_name' => $row['first_name'],
										'mobile' => $row['mobile'],
										'message' => $validateError[0]
									);
				} 
			}else{
				if(!empty($row['state'])){

					$row['state'] = trim($row['state']);

					$state = State::select('id','name')
					->whereRaw('LOWER(name) like ?', '%'.strtolower($row['state']).'%')
					->first();
					if($state){
						$stateId =  $state->id;
					}else{
						$stateId =  Null;
					}
				}else{
					$stateId =  Null;
				}

				/*if(!empty($row['state']) && $stateId == Null){
		        	$importError = 1;
		        	$errors[] = 	array(
			    						'row' => $rowNo,
										'first_name' => $row['first_name'],
										'mobile' => $row['mobile'],
										'message' => 'State not found'
			    					);
		        }*/

		        if(!empty($row['district']) && !is_null($stateId)){
		        	$row['district'] = trim($row['district']);
					$district = District::select('id','name')
					->whereRaw('LOWER(name) like ?', '%'.strtolower($row['district']).'%')
					->where('state_id',$stateId)
					->first();
					if($district){
						$districtId =  $district->id;
					}else{
						$districtId =  Null;
					}
				}else{
					$districtId =  Null;
				}
				if(!empty($row['district']) && !is_null($stateId) && is_null($districtId)){
					$district = new District();
					$district->name = $row['district'];
					$district->state_id = $stateId;
					$district->region_id = $stateId;
					$district->status = 1;
					$district->save();
					$districtId = $district->id;
				}

				/*if(!empty($row['district']) && $districtId == Null){
		        	$importError = 1;
		        	$errors[] = 	array(
			    						'row' => $rowNo,
										'first_name' => $row['first_name'],
										'mobile' => $row['mobile'],
										'message' => 'District not found'
			    					);
		        }*/

		        if(!empty($row['city']) && !is_null($districtId)){
		        	$row['city'] = trim($row['city']);
					$city = City::select('id','name')
					->whereRaw('LOWER(name) like ?', '%'.strtolower($row['city']).'%')
					->where('district_id',$districtId)
					->first();
					if($city){
						$cityId =  $city->id;
					}else{
						$cityId =  Null;
					}
				}else{
					$cityId =  Null;
				}

				if(!empty($row['city']) && !is_null($stateId) && !is_null($districtId) && is_null($cityId)){
					$city = new City();
					$city->name = $row['city'];
					$city->state_id = $stateId;
					$city->district_id = $districtId;
					$city->status = 1;
					$city->save();
					$cityId = $city->id;
				}

				/*if(!empty($row['city']) && $cityId == Null){
		        	$importError = 1;
		        	$errors[] = 	array(
			    						'row' => $rowNo,
										'first_name' => $row['first_name'],
										'mobile' => $row['mobile'],
										'message' => 'city not found'
			    					);
		        }*/

				if($importError == 0){
					$user = new User();
					$created_by = Auth::user()->id;

					$mobile  = $row['mobile'];
					if(empty($row['mobile'])){
						$mobile  = Null;
					}

					$email  = $row['email'];
					if(empty($row['email'])){
						$email  = 'profitley_'.$rowNo.'@yopmail.com';
					}

					$user->organization_id 		= $this->organization_id;
					$user->name                 = $row['first_name'];
					$user->last_name            = $row['last_name'];
					$user->email            	= $email;
					$user->password         	= \Hash::make('profitley@123');
					$user->phone_number			= $mobile;
					$user->shop_name			= $row['shop_name'];
					$user->address1				= $row['address'];
					$user->country				= 103;
					$user->state				= $stateId;
					$user->pincode				= $row['pincode'];
					$user->district				= $districtId;
					$user->city					= $cityId;
					$user->is_approved          = 1;
					$user->created_by           = $created_by;
					if($user->save()){
						$success[] = $rowNo;
						$item[] = array(
											'user_id' 		=> $user->id,
										);

						$assignOrganization = new OrganizationBuyer();
	                    $assignOrganization->organization_id = $this->organization_id;

	                    if(!empty($row['category'])){
	                    	if(strtolower($row['category']) == 'a'){
	                    		$assignOrganization->buyer_category = 1;
	                    	}elseif(strtolower($row['category']) == 'b'){
	                    		$assignOrganization->buyer_category = 2;
	                    	}elseif(strtolower($row['category']) == 'c'){
	                    		$assignOrganization->buyer_category = 3;
	                    	}elseif(strtolower($row['category']) == 'd'){
	                    		$assignOrganization->buyer_category = 4;
	                    	}elseif(strtolower($row['category']) == 'e'){
	                    		$assignOrganization->buyer_category = 5;
	                    	}
	                    }else{
	                    	$assignOrganization->buyer_category = 1;
	                    }
	                    $assignOrganization->credit_limit = 0;
	                    if(!empty($row['credit_limit'])){
	                    	$assignOrganization->credit_limit = $row['credit_limit'];
	                    }
	                    $assignOrganization->buyer_id = $user->id;
	                    $assignOrganization->status = 1;
	                    $assignOrganization->save();

	                    if(!empty($row['address'])){
		                    $billing = new Address();
		                    $billing->user_id = $user->id;
		                    $billing->address_type = 1;
		                    $billing->name = 'Billing Address';
		                    $billing->address1 = $row['address'];
		                    $billing->country = 103;
		                    $billing->state = $stateId;
		                    $billing->district = $districtId;
		                    $billing->city = $cityId;
		                    $billing->pincode = $row['pincode'];
		                    $billing->is_active = 1;
		                    $billing->created_by = $created_by;
		                    $billing->save();

		                    $shipping = new Address();
		                    $shipping->user_id = $user->id;
		                    $shipping->address_type = 1;
		                    $shipping->name = 'Shipping Address';
		                    $shipping->address1 = $row['address'];
		                    $shipping->country = 103;
		                    $shipping->state = $stateId;
		                    $shipping->district = $districtId;
		                    $shipping->city = $cityId;
		                    $shipping->pincode = $row['pincode'];
		                    $shipping->is_active = 1;
		                    $shipping->created_by = $created_by;
		                    $shipping->save();
	                    }

	                    $modelRole = new ModelRole();
		                $modelRole->role_id = $roleId;
		                $modelRole->model_type = 'Modules\User\Entities\User';
		                $modelRole->model_id = $user->id;
		                $modelRole->save();

					}
				}
			}
		}
		if(!empty($item)){
			/*foreach ($item as $key => $newItem) {
				$newUser = User::create($newItem);
			}*/
			$this->data = 	array(
								'success' => $success,
								'errors' => $errors
							);
		}else{
			if(!empty($errors)){
				$this->data = 	array(
								'success' 	=> array(),
								'errors' 	=> $errors
							);
			} else{

				$this->data = 	array(
									'success' 	=> array(),
									'errors' 	=> array(
													'row' => 1,
													'message' => 'There seems some error in importing file.'
												)
								);
			}
		}
	}

	public function chunkSize(): int
	{
		return 1500;
	}
}
