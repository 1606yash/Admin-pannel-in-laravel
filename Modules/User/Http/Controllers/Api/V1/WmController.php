<?php

namespace Modules\User\Http\Controllers\Api\V1;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Entities\Role;
use Modules\User\Entities\Address;
use Modules\User\Entities\User;
use Modules\User\Entities\RetailerCategories;
use Modules\User\Entities\State;
use Modules\User\Entities\City;
use Modules\User\Entities\District;
use Modules\User\Entities\OrganizationBuyer;
use Modules\User\Entities\RetailerMapping;
use Modules\User\Http\Requests\UserRequest;
use DB;
use Image;
use DataTables;
use App\Http\Controllers\ApiBaseController;
use Auth;
use Modules\Saas\Entities\Organization;
use Modules\Saas\Entities\Settings;
use Modules\User\Entities\ModuleFeature;
use Modules\User\Entities\OrganizationPermission;
use Modules\Saas\Entities\HomeSetting;
use Modules\Ecommerce\Entities\Wishlist;
use Modules\User\Transformers\UserPresenter;
use Modules\Administration\Entities\Contact;
use Modules\User\Entities\ModelRole;
use Modules\User\Entities\OrganizationStaff;
use Modules\Administration\Entities\Menu;

class WmController extends ApiBaseController
{
    public function __construct() {
        $this->success =  '200';
        $this->ok =  '200';
        $this->accessDenied =  '400';
    }
    
    public function getAllBuyers(Request $request, $user_type = "")
    {
        try {

            $user = Auth::user();
            $role = $user->getRoleNames()->toArray();


            if($role[0] == \Config::get('constants.ROLES.SP')){
                $retailers =    RetailerMapping::
                                select(\DB::raw('group_concat(retailer_id) as retailer_id'))
                                ->where('dsp_id',$user->id)
                                ->groupBy('dsp_id')
                                ->first();
            }else{
                $retailers = array();
            }

            $users =    User::from('users as u')
                        ->select('u.id', 'u.organization_id', 'u.name', 'u.last_name', 'u.email', 'u.phone_number', 'u.file', 'u.original_name', 'u.shop_name', 'u.gst', 'ob.buyer_category as retailer_category','rc.retailer_catagory as category', 'ob.status', 'u.address1', 'u.address2', 'u.country', 'u.state', 'u.pincode', 'u.district', 'u.city', 'u.created_at','r.id as role_id','r.name as role','r.label as roleName','s.name as stateName','c.name as cityName','d.name as districtName')
                        ->leftJoin('states as s','s.id','=','u.state')
                        ->leftJoin('cities as c','c.id','=','u.city')
                        ->leftJoin('districts as d','d.id','=','u.district')
                        ->leftJoin('model_has_roles as mr','mr.model_id','=','u.id')
                        ->leftJoin('roles as r','r.id','=','mr.role_id')
                        ->leftJoin('organization_buyer as ob','u.id','=','ob.buyer_id')
                        ->leftJoin('retailer_catagory as rc','rc.id','=','ob.buyer_category')
                        ->where('u.is_approved',1)
                        ->where('ob.status',1)
                        ->where('r.name',\Config::get('constants.ROLES.BUYER'))
                        ->where(function ($query) use ($retailers) {
                            if (!empty($retailers)) {
                                $query->whereIn('u.id',explode(',',$retailers->retailer_id));
                            }
                        })
                        ->orderBy('u.name','asc')
                        ->groupBy('u.id')
                        ->get();

            $users = (new UserPresenter())->present($users);
            return $this->sendSuccessResponse($users, $this->success);
            
        } catch (\Exception $exception) {
            return $this->sendFailureResponse($exception->getMessage());
        }
    }
    
    
}
