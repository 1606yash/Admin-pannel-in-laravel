<?php
namespace Modules\User\Transformers;

use League\Fractal\TransformerAbstract;
use Auth;
use Modules\User\Entities\Role;
use Modules\User\Entities\Address;
use Modules\User\Entities\User;
use Modules\User\Entities\RetailerCategories;
use Modules\User\Entities\OrganizationBuyer;
use Modules\User\Entities\RetailerMapping;
use Modules\Ecommerce\Entities\Order;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        if(isset($user->organization_id)){
            $organizationId = $user->organization_id;
        }else{
            $organizationId = Auth::user()->organization_id;
        }

        if(!is_null($user->file)){
            $file = url('uploads/users/') .'/'. $user->file;
        }else{
            $file = '';
        }

        $role = $user->role;

        $transformedArray = [
            'id' => $user->id,
            'full_name' => $user->FullName,
            'name' => $user->name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role' => $role,
            'picture' => $file,
            'picture_thumb' => $file,
            'phone_country_code' => $user->phone_country_code,
            'phone_number' => $user->phone_number,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'address1' => $user->address1,
            'address2' => $user->address2,
            'state' => $user->state,
            'stateName' => $user->stateName,
            'district' => $user->district,
            'districtName' => $user->districtName,
            'city' => $user->city,
            'cityName' => $user->cityName,
            'pincode' => $user->pincode,
            'status' => $user->status,
        ];

        if(strtolower($role) == \Config::get('constants.ROLES.SP')){
            $retailers =    RetailerMapping::
                            select(\DB::raw('group_concat(retailer_id) as retailer_id'))
                            ->where('dsp_id',$user->id)
                            ->groupBy('dsp_id')
                            ->first();

            if($retailers){
                $transformedArray['retailers'] = explode(',',$retailers->retailer_id);
            }else{
                $transformedArray['retailers'] = array();
            }

        }

        if($role == \Config::get('constants.ROLES.BUYER')){

            $category = $user->retailer_category;
            $buyer_category = null;
            $credit_limit = 0;
            $status = 0;

            $retailerData = OrganizationBuyer::select('rc.retailer_catagory as category','rc.id as buyer_category','organization_buyer.credit_limit','organization_buyer.status')
                        ->leftJoin('retailer_catagory as rc','rc.id','=','organization_buyer.buyer_category')
                        ->where('buyer_id',$user->id)->first();

            if($retailerData){
                $category = $retailerData->category;
                $buyer_category = $retailerData->buyer_category;
                $credit_limit = $retailerData->credit_limit;
                $status = $retailerData->status;
            }

            

            $transformedArray['shop_name'] = $user->shop_name;
            $transformedArray['gst'] = $user->gst;
            $transformedArray['buyer_category'] = $buyer_category;
            $transformedArray['category'] = $category;
            $transformedArray['credit_limit'] = $user->credit_limit;
            // $transformedArray['used_credit'] = 5000; //Accept,Processing,Shipped,Invoiced
            // $transformedArray['lastOrderDate'] = $lastOrderDate;
            $transformedArray['status'] = $status;
        }

        return $transformedArray;
    }
}