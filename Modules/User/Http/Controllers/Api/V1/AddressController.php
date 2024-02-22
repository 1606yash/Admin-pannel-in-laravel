<?php

namespace Modules\User\Http\Controllers\Apis\V1;

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
use Modules\User\Http\Requests\UserRequest;
use DB;
use Image;
use DataTables;
use App\Http\Controllers\ApiBaseController;
use Auth;
use Modules\Saas\Entities\Organization;
use Modules\Saas\Entities\Settings;

class AddressController extends ApiBaseController
{

    public function __construct() {
        $this->success =  '200';
        $this->ok =  '200';
        $this->accessDenied =  '400';
    }


    /**
     * @OA\Get(
     *     path="/api/v1/addresses",
     *     tags={"User"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="API to get addresses of user",
     *     operationId="indexad",
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    
    public function index(Request $request)
    {

        $user = \Auth::user();
        $organizationId=$user->organization_id;
        $userId=$user->id;

        $addresses =    Address::from('addresses as a')
                        ->select('a.id','a.user_id','a.is_default', 'a.name', 'a.address1', 'a.address2', 'a.country', 'a.state as state_id', 'a.district as district_id', 'a.city as city_id', 'a.pincode','s.name as state','c.name as city','d.name as district','co.name as country',
                            DB::Raw('case when (address_type = 1) then "Billing" else "Shipping" end AS address_type')
                        )
                        ->leftJoin('countries as co','co.id','=','a.country')
                        ->leftJoin('states as s','s.id','=','a.state')
                        ->leftJoin('districts as d','d.id','=','a.district')
                        ->leftJoin('cities as c','c.id','=','a.city')
                        ->where('a.user_id',$userId)
                        ->where('is_active',1)
                        ->orderBy('a.created_at','DESC')
                        ->get();

        $data['message'] = 'All addresses of user.';
        $data['addresses'] = $addresses;
        return $this->sendSuccessResponse($data, $this->success);
    }
    
    /**
     * @OA\Post(
     *     path="/api/v1/addresses",
     *     tags={"User"},
     *     summary="Create address",
     *     operationId="storeaaa",
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="This api will create new address.",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="address_type",type="number",example="1:Billing,2:Shipping"),
     *                 @OA\Property(property="is_default",type="number",example=1),
     *                 @OA\Property(property="name",type="string",example="John wick"),
     *                 @OA\Property(property="address1",type="string",example="Wall Street 1"),
     *                 @OA\Property(property="address2",type="string",example="Near ATM"),
     *                 @OA\Property(property="country",type="number",example=103),
     *                 @OA\Property(property="state",type="number",example=21),
     *                 @OA\Property(property="district",type="number",example=21),
     *                 @OA\Property(property="city",type="number",example=21),
     *                 @OA\Property(property="pincode",type="number",example=741258),
     *             ),
     * 
     *         )
     *     ),
     *     @OA\Response(response=200,description="OK"),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $user = \Auth::user();
        $organizationId=$user->organization_id;
        $userId=$user->id;

        $address = new Address();

        $address->user_id = $userId;
        $address->address_type = $request->address_type;
        $address->is_default = $request->is_default;
        $address->name = $request->name;
        $address->address1 = $request->address1;
        $address->address2 = $request->address2;
        $address->country = $request->country;
        $address->state = $request->state;
        $address->district = $request->district;
        $address->city = $request->city;
        $address->pincode = $request->pincode;
        $address->is_active = 1;
        $address->created_by = $userId;

        if($address->save()){


            $newAddress =    Address::from('addresses as a')
                            ->select('a.id','a.user_id','a.is_default', 'a.name', 'a.address1', 'a.address2', 'a.country', 'a.state as state_id', 'a.district as district_id', 'a.city as city_id', 'a.pincode','s.name as state','c.name as city','d.name as district','co.name as country',
                                DB::Raw('case when (address_type = 1) then "Billing" else "Shipping" end AS address_type')
                            )
                            ->leftJoin('countries as co','co.id','=','a.country')
                            ->leftJoin('states as s','s.id','=','a.state')
                            ->leftJoin('districts as d','d.id','=','a.district')
                            ->leftJoin('cities as c','c.id','=','a.city')
                            ->where('a.id',$address->id)
                            ->where('is_active',1)
                            ->orderBy('a.created_at','DESC')
                            ->first();

            $data['message'] = 'Address added successfully.';
            $data['address'] = $newAddress;
            return $this->sendSuccessResponse($data, $this->success);
        }else{
            return $this->sendFailureResponse('Something went wrong.');
        }

    }

    /**
     * @OA\Put(
     *     path="/api/v1/addresses/{address_id}",
     *     tags={"User"},
     *     summary="Update address",
     *     operationId="updatead",
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="This api will update address.",
     *     @OA\Parameter(name="address_id",in="path",required=true,
     *     description="Id of address",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="address_type",type="number",example="1:Billing,2:Shipping"),
     *                 @OA\Property(property="is_default",type="number",example=1),
     *                 @OA\Property(property="name",type="string",example="John wick"),
     *                 @OA\Property(property="address1",type="string",example="Wall Street 1"),
     *                 @OA\Property(property="address2",type="string",example="Near ATM"),
     *                 @OA\Property(property="country",type="number",example=103),
     *                 @OA\Property(property="state",type="number",example=21),
     *                 @OA\Property(property="district",type="number",example=21),
     *                 @OA\Property(property="city",type="number",example=21),
     *                 @OA\Property(property="pincode",type="number",example=741258),
     *                 @OA\Property(property="status",type="number",example=1),
     *             ),
     * 
     *         )
     *     ),
     *     @OA\Response(response=200,description="OK"),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function update(Request $request,$address_id)
    {
        $user = \Auth::user();
        $organizationId=$user->organization_id;
        $userId=$user->id;

        $address = Address::findorfail($address_id);

        $address->user_id = $userId;
        $address->address_type = $request->address_type;
        $address->is_default = $request->is_default;
        $address->name = $request->name;
        $address->address1 = $request->address1;
        $address->address2 = $request->address2;
        $address->country = $request->country;
        $address->state = $request->state;
        $address->district = $request->district;
        $address->city = $request->city;
        $address->pincode = $request->pincode;

        if($request->exists('status')){
            $address->is_active = $request->status;
        }

        if($address->save()){
            $data['message'] = 'Address updated successfully.';
            $data['address'] = $address;
            return $this->sendSuccessResponse($data, $this->success);
        }else{
            return $this->sendFailureResponse('Something went wrong.');
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/v1/addresses/{address_id}",
     *     summary="Delete address",
     *     operationId="destroy",
     *     tags={"User"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="This api will delete address.",
     *     @OA\Parameter(name="address_id",in="path",required=true,
     *     description="Id of address",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function destroy($address_id){
        try {
            DB::beginTransaction();
            $user = \Auth::user();
            $organizationId=$user->organization_id;
            $userId=$user->id;
            if($address_id != 0){
                $address = Address::findorfail($address_id);
                if($address->delete()){
                    $data['message'] = 'Address removed successfully.';
                    DB::commit();
                    return $this->sendSuccessResponse($data, $this->success);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendFailureResponse($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{id}/address",
     *     tags={"User"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="API to get addresses by user id",
     *     operationId="userAddresses",
     *     @OA\Parameter(name="user_id",in="path",required=true,
     *     description="id of the user",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    
    public function userAddresses(Request $request,$user_id)
    {

        $user = \Auth::user();
        $organizationId=$user->organization_id;

        $addresses =    Address::from('addresses as a')
                        ->select('a.id','a.user_id','a.is_default', 'a.name', 'a.address1', 'a.address2', 'a.country', 'a.state as state_id', 'a.district as district_id', 'a.city as city_id', 'a.pincode','s.name as state','c.name as city','d.name as district','co.name as country',
                            DB::Raw('case when (address_type = 1) then "Billing" else "Shipping" end AS address_type')
                        )
                        ->leftJoin('countries as co','co.id','=','a.country')
                        ->leftJoin('states as s','s.id','=','a.state')
                        ->leftJoin('districts as d','d.id','=','a.district')
                        ->leftJoin('cities as c','c.id','=','a.city')
                        ->where('a.user_id',$user_id)
                        ->where('is_active',1)
                        ->orderBy('a.created_at','DESC')
                        ->get();

        $data['message'] = 'All addresses of user.';
        $data['addresses'] = $addresses;
        return $this->sendSuccessResponse($data, $this->success);
    }
}
