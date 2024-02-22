<?php

namespace Modules\Administration\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Administration\Entities\Notifications;
use Modules\Administration\Entities\NotificationTemplate;
use Modules\User\Entities\User;
use Image;
use Auth;
use DataTables;
use DB;
use URL;


class NotificationController extends Controller
{

    public function __construct() {

        /* Execute authentication filter before processing any request */
        $this->middleware('auth');

        if (\Auth::check()) {
            return redirect('/');
        }

    }
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $userPermission = \Session::get('userPermission');
        if(!isset($userPermission[\Config::get('constants.FEATURES.NOTIFICATION_TEMPLATES')]))
            return view('error/403');

        $user = Auth::user();
        $organizationId=$user->organization_id;

        $data = NotificationTemplate::where('notification_templates.organization_id',$organizationId)
                    ->whereNull('notification_templates.deleted_at')
                    ->orderBy('notification_templates.updated_at','DESC')
                    ->orderBy('notification_templates.created_at','DESC')
                    ->get();

        if ($request->ajax()) {
            
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) use ($userPermission){            
                        $edit = URL::to('/').'/administration/notification/templates/edit/'.$row->id;
                        $btn = '';
                        $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                        if(isset($userPermission['notification_templates']) && ($userPermission['notification_templates']['edit_all'] || $userPermission['notification_templates']['edit_own'])){

                            $btn .= "<li>
                                        <a href='".$edit."'>
                                            <em class='icon ni ni-edit'></em> <span>Edit</span>
                                        </a>
                                    </li>";
                        }
                        
                        $btn .= "</ul></div></div></li></ul>";
                        return $btn;
                    })
                    ->addColumn('created_at', function ($row) {
                        return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->created_at));
                    })
                    ->addColumn('updated_at', function ($row) {
                        return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->updated_at));
                    })
                    
                    ->rawColumns(['action','created_at','updated_at'])
                    ->make(true);
        } 
        return view('administration::notification/index',['notiTemplates'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($id)
    {
        $user = Auth::user();
        $organizationId=$user->organization_id;
        $data = NotificationTemplate::where('notification_templates.id',$id)->first();
        $users = User::from('users as u')
                ->select('u.id','u.name','u.last_name','u.email','u.file','u.phone_number','u.created_at','u.updated_at','r.name as role','r.label as roleName')
                ->leftJoin('model_has_roles as mr','mr.model_id','=','u.id')
                ->leftJoin('roles as r','mr.role_id','=','r.id')
                ->where('r.name','!=',\Config::get('constants.ROLES.BUYER'))
                ->where('u.organization_id',$organizationId)
                ->orderby('u.id','desc')
                ->get();
                //all users excpet buyers
        /*echo '<pre>';
        print_r($data->toArray());die;*/
        return view('administration::notification/edit',['notiTemplate'=>$data,'users'=>$users]);
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
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('administration::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('administration::notification/eddsaasit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request)
    {
       /* echo '<pre>';
        print_r(request()->all());die;*/
        //$user = Auth::user();
        try {

            $rules = array(
                // 'title' => 'required',
                'id' => 'required'
            );
            
            $validator = \Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                $messages = $validator->messages();
                return redirect('administration/notification/templates')->with('error', $messages);
            } else {


                if($request->input("id") && $request->input("id")!='0' && $request->input("id")!=''){
                    
                    $template = NotificationTemplate::find($request->input("id"));
                    $msg = 'Template Updated Successfully.';

                }else{
                    return redirect('administration/notification/templates')->with('error', 'Something went wrong!');
                }

                $custom = explode(',',$request->customEmails);
                $extras = json_encode(array('bcc_users' => $request->users,'custom'=>$custom),JSON_UNESCAPED_SLASHES);


                if(!isset($request->mail)){
                    $mail = "";
                }else{
                    $mail = $request->mail;
                }

                if(isset($request->email_subject)){
                    $email_subject = $request->email_subject;
                }else{
                    $email_subject = "";
                }

                if(!isset($request->database)){
                    $database = "";
                }else{
                    $database = $request->database;
                }
                if(!isset($request->wa)){
                    $wa = "";
                }else{
                    $wa = $request->wa;
                }

                if(!isset($request->web)){
                    $web = "";
                }else{
                    $web = $request->web;
                }

                $viaArray=[];

                $body = json_encode(array('mail' => $mail,'database'=>$database,'wa'=>$wa,'web'=>$web),JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT);
                
                if($request->whatsApp){
                    array_push($viaArray,'wa');
                }
                if($request->email){
                    array_push($viaArray,'mail');
                }
                if($request->web){
                    array_push($viaArray,'database');
                }

                $via = json_encode($viaArray,JSON_UNESCAPED_SLASHES);
                $postVia =stripslashes($via);
                $postExtras=stripslashes($extras);
                $postBody=stripslashes($body);
                //$template->via=$postVia;
                //$template->extras=$postExtras;
                //$template->body=$postBody;

                $friendly_name = $request->name;

                $update = DB::table('notification_templates')
                ->where('id', $request->id)  
                ->limit(1)  
                ->update(array('friendly_name' => $friendly_name,'email_subject' => $email_subject,'body' => $postBody,'via'=>$postVia,'extras'=>$postExtras));
                if($update){
                    return redirect('administration/notification/templates')->with('message', $msg);
                }else{
                    return redirect('administration/notification/templates')->with('error', 'Something went wrong!');
                }
            }
            
        } catch (Exception $e) {
            return redirect('administration/notification/templates')->with('error', 'Something went wrong!');
        }
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
