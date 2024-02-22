<?php

namespace Modules\Administration\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Administration\Entities\Contact;

class ContactusController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(Request $request)
    {
        $userPermission = \Session::get('userPermission');

        // if(!isset($userPermission[\Config::get('constants.FEATURES.CONTACT_US')])){
        //     return view('error/403');
        // }

        $user = \Auth::user();
        
        $perPage = \Config::get('constants.PAGE.PER_PAGE');
        
        if(isset($request->perPage) && $request->perPage > 0){
            $perPage = $request->perPage;
        }
        $contacts = User::paginate($perPage);
        return view('administration::contactus/index',['contacts' => $contacts]);
    }

    public function show(Request $request)
    {
        $user = \Auth::user();
        $contact = Contact::where('id',$request->id)->first();

        if($contact){
            $contact->contact_date = date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($contact->created_at));

            return array('success' =>true,'contact'=>$contact);
        }else{
            return array('success' =>false);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('administration::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('administration::notification/edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
