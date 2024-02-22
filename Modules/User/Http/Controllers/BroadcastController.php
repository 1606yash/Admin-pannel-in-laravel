<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\User\Entities\User;
use DataTables;
use Modules\User\Entities\Broadcast;
use DB;
use Auth;
use SendNotification;

class BroadcastController extends Controller
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
        if(!isset($userPermission[\Config::get('constants.FEATURES.BROADCAST')]))
            return view('error/403');

        $user = Auth::user();
        $organizationId=$user->organization_id;
        $perPage = \Config::get('constants.PAGE.PER_PAGE');
        if(isset($request->perPage) && $request->perPage > 0){
            $perPage = $request->perPage;
        }

        $data = Broadcast::from('broadcasts as bc')->select('bc.id','bc.type','bc.receivers','bc.receivers','rc.retailer_catagory','bc.message','bc.created_at')
        ->leftJoin('retailer_catagory as rc','rc.id','=','bc.buyer_category')
        ->where('bc.organization_id',$organizationId)
        ->orderBy('bc.id','DESC')->get();
                            //->paginate($perPage);
        
        if ($request->ajax()) {
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row) use($userPermission){
             $resend = url('/').'/broadcast/resend/'.$row->id;
             
             if(isset($userPermission['broadcast']) && ($userPermission['broadcast']['delete_all'] || $userPermission['broadcast']['delete_own'])){
                $deleteBtn = "<li>
                <a href='#' data-id='".$row->id."' class='eg-swal-av3'>
                <em class='icon ni ni-trash'></em> <span>Delete</span>
                </a>
                </li>";
            }else{
                $deleteBtn = '';
            }

            $btn = "
            <ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>
            <li>
            <a href='".$resend."'>
            <em class='icon ni ni-mail-fill'></em> <span>Resend</span>
            </a>
            </li>
            ".$deleteBtn."
            </ul></div></div></li></ul>
            ";
            return $btn;
        })
            ->addColumn('created_at', function ($row) {
                        //return ($event->is_private == true) ? 'Invite Only' : 'Public';
                return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->created_at));
            })
            ->addColumn('receivers', function ($row) {
                return count(explode(',',$row->receivers));
            })
            ->addColumn('type', function ($row) {
                        //return ($event->is_private == true) ? 'Invite Only' : 'Public';
                if($row->type==1){
                    $type='All Buyers';
                }else if($row->type==2){
                    $type='Specific Buyer';
                }else{
                    $type='Buyer Category';
                }
                return $type;
            })
            ->rawColumns(['action','created_at','type'])
            ->make(true);
        }

        $buyers =    User::from('users as u')
        ->select('u.id', 'u.name', 'u.last_name', 'u.shop_name', 'u.email', 'u.file', 'u.phone_number', 'ob.organization_id as obid')
        ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
        ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
        ->leftJoin('organization_buyer as ob', 'u.id', '=', 'ob.buyer_id')
        ->where('r.name', \Config::get('constants.ROLES.BUYER'))
        ->where('ob.organization_id', $organizationId)
        ->orderby('u.name', 'asc')
        ->get();

        $buyerCategories = array();
        return view('user::broadcast/index',['broadcasts'=>$data,'buyers'=>$buyers,'buyerCategories'=>$buyerCategories]);
    }

    public function table(Request $request)
    {
        $user = \Auth::user();
        $organizationId=$user->organization_id;
        
        
        if ($request->ajax()) {
            $data = Brand::where('organization_id',$organizationId)->get();
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
             
             $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">View</a>';
             
             return $btn;
         })
                    /*->setRowClass(function ($row) {
                        return 'nk-tb-item';
                    })*/
                    ->rawColumns(['action'])
                    ->make(true);
                }

                return view('user::broadcast/table');
            }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function map()
    {
        return view('user::mapping/map');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        /*echo '<pre>';
        print_r(request()->all());die;*/
        //
        try {

            $rules = array(
                'message' => 'required',
                'br_type' => 'required'     
            );
            $user = \Auth::user();
            $organizationId=$user->organization_id;
            $validator = \Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                $messages = $validator->messages();
                return redirect('broadcast')->with('error', $messages);
            } else {
                $i=0;
                
                if($request->input("br_type") && $request->input("br_type")==1)
                {
                    $buyerIds = User::from('users as u')
                    ->select(DB::raw('group_concat(u.id) as ids'))
                    ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    ->join('roles as r','r.id','=','mr.role_id')
                    ->where('u.organization_id',$organizationId)
                    ->where('r.name',\Config::get('constants.ROLES.BUYER'))
                    ->orderBy('u.name','asc')
                    ->get();

                    if(!empty($buyerIds->toArray())){
                        $ids=$buyerIds->toArray()[0]['ids'];
                    }else{
                        $ids='';
                    }

                    $buyers = User::from('users as u')
                    ->select('u.id','u.name','u.phone_number','u.email')
                    ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    ->join('roles as r','r.id','=','mr.role_id')
                    ->where('u.organization_id',$organizationId)
                    ->where('r.name',\Config::get('constants.ROLES.BUYER'))
                    ->orderBy('u.name','asc')
                    ->get();

                    if(!empty($buyers->toArray())){

                        $broadcast = new Broadcast;
                        $broadcast->type = 1;
                        $broadcast->receivers = $ids;
                        $broadcast->message = $request->input("message");
                        $broadcast->is_active = 1;
                        $broadcast->organization_id = $organizationId;


                        foreach ($buyers as $key => $value) {
                            
                            $to_name = $value->name;
                            $to_email = $value->email;
                            $data = array('name'=>$receiver->name, "body" => $request->input("message"),'mailSubject' => 'Brodcast Mail');
                            $mailBody = $request->input("message");

                            Mail::send('emails.email_template', $data, function ($message)  use ($to_name, $to_email,$mailBody,$mailSubject) {
                                // $message->to($to_email, $to_name)
                                $message->to($to_email, $to_name)
                                ->subject($mailSubject)
                                ->from('support@profitley.com','Profitley');
                                // ->setBody($mailBody, 'text/html');
                            });
                        }


                        if($broadcast->save()){
                            $i++;
                        }
                    }
                }else if($request->input("br_type") && $request->input("br_type")==2)
                {
                    
                    if(!empty($request->input("buyer"))){
                        $ids=implode(',',$request->input("buyer"));
                    }else{
                        $ids='';
                    }

                    $buyers = User::from('users as u')
                    ->select('u.id','u.name','u.phone_number','u.email')
                    ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    ->join('roles as r','r.id','=','mr.role_id')
                    ->where('u.organization_id',$organizationId)
                    ->where('r.name',\Config::get('constants.ROLES.BUYER'))
                    ->whereIn('u.id',$request->input("buyer"))
                    ->get();

                    if(!empty($buyers->toArray())){

                        $broadcast = new Broadcast;
                        $broadcast->type = 2;
                        $broadcast->receivers = $ids;
                        $broadcast->message = $request->input("message");
                        $broadcast->is_active = 1;
                        $broadcast->organization_id = $organizationId;

                        foreach ($buyers as $key => $value) {
                            
                            $to_name = $value->name;
                            $to_email = $value->email;
                            $mailSubject = 'Brodcast Mail';
                            $data = array('name'=>$value->name, "body" => $request->input("message"),'mailSubject' => $mailSubject);
                            $mailBody = $request->input("message");

                            Mail::send('emails.email_template', $data, function ($message)  use ($to_name, $to_email,$mailBody,$mailSubject) {
                                // $message->to($to_email, $to_name)
                                $message->to($to_email, $to_name)
                                ->subject($mailSubject)
                                ->from('support@profitley.com','Profitley');
                                // ->setBody($mailBody, 'text/html');
                            });
                        }

                        if($broadcast->save()){
                            $i++;
                        }
                    }
                }else{
                    //buyerCat
                    $buyerIds = User::from('users as u')
                    ->select(DB::raw('group_concat(u.id) as ids'))
                    ->leftJoin('organization_buyer as ob', 'u.id', '=', 'ob.buyer_id')
                    ->join('retailer_catagory as rc','rc.id','=','ob.buyer_category')
                    ->where('ob.organization_id',$organizationId)
                    ->where('u.retailer_category',$request->input("buyerCat"))
                    ->orderBy('u.name','asc')
                    ->get();

                    if(!empty($buyerIds->toArray())){
                        $ids=$buyerIds->toArray()[0]['ids'];
                    }else{
                        $ids='';
                    }

                    $buyers = User::from('users as u')
                    ->select('u.id','u.name','u.phone_number','u.email')
                    ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    ->join('roles as r','r.id','=','mr.role_id')
                    ->where('u.organization_id',$organizationId)
                    ->where('r.name',\Config::get('constants.ROLES.BUYER'))
                    ->whereIn('u.id',explode(',',$ids))
                    ->get();

                    if(!empty($buyers->toArray())){

                        $broadcast = new Broadcast;
                        $broadcast->type = 3;
                        $broadcast->receivers = $ids;
                        $broadcast->message = $request->input("message");
                        $broadcast->buyer_category = $request->input("buyerCat");
                        $broadcast->is_active = 1;
                        $broadcast->organization_id = $organizationId;

                        foreach ($buyers as $key => $value) {
                            
                            $to_name = $value->name;
                            $to_email = $value->email;
                            $data = array('name'=>$receiver->name, "body" => $request->input("message"),'mailSubject' => 'Brodcast Mail');
                            $mailBody = $request->input("message");

                            Mail::send('emails.email_template', $data, function ($message)  use ($to_name, $to_email,$mailBody,$mailSubject) {
                                // $message->to($to_email, $to_name)
                                $message->to($to_email, $to_name)
                                ->subject($mailSubject)
                                ->from('support@profitley.com','Profitley');
                                // ->setBody($mailBody, 'text/html');
                            });
                        }

                        if($broadcast->save()){
                            $i++;
                        }
                    }
                }


                // if($request->br_type == 1){
                //     $retailers =    User::from('users as u')
                //     ->select('u.id as user_id','u.phone_number', 'ob.organization_id as obid','u.fcm_token')
                //     ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
                //     ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
                //     ->leftJoin('organization_buyer as ob', 'u.id', '=', 'ob.buyer_id')
                //     ->where('r.name', \Config::get('constants.ROLES.BUYER'))
                //     ->where('ob.organization_id', $organizationId)
                //     ->orderby('u.name', 'asc')
                //     ->get();
                    
                // }elseif($request->br_type == 2){
                //     $retailers = User::select('id as user_id','phone_number','fcm_token')->whereIn('id',$request->buyer)->get();

                // }elseif($request->br_type == 3){
                //     $retailers =    User::from('users as u')
                //     ->select('u.id as user_id','u.phone_number','u.fcm_token')
                //     ->leftJoin('model_has_roles as mr','mr.model_id','=','u.id')
                //     ->leftJoin('roles as r','r.id','=','mr.role_id')
                //     ->leftJoin('organization_buyer as ob', 'u.id', '=', 'ob.buyer_id')
                //     ->join('retailer_catagory as rc','rc.id','=','ob.buyer_category')
                //     ->where('u.retailer_category',$request->buyerCat)
                //     ->where('r.name',\Config::get('constants.ROLES.BUYER'))
                //     ->where('ob.organization_id', $organizationId)
                //     ->groupBy('u.id')
                //     ->get();
                // }

                // $retailerNumbers = array();
                // if(!empty($retailers->toArray())){


                //     foreach ($retailers as $key => $retailer) {
                //         $retailerNumbers[] = '91'.$retailer->phone_number;
                        
                //         if(!is_null($retailer->fcm_token)){
                //             $details = [
                //                 'title' => 'Profitley-Alert',
                //                 'body' => $request->message,
                //                 'user_id' => $retailer->user_id,
                //                 'fcm_token' => $retailer->fcm_token
                //             ];

                //             $sendNotification = SendNotification::sendNotification($details);
                //         }
                //     }

                //     if(!empty($retailerNumbers)){
                //         $retailerNumbers = implode(',', $retailerNumbers);
                //         $broadcast = \Helpers::sendWaNotification($request->message,$retailerNumbers);
                //     }

                // }
                
                if($i>0){
                    return redirect('broadcast')->with('message',trans('messages.BROADCAST_SUCCESS'));
                }else{
                    return redirect('broadcast')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
                }
            }
            
        } catch (Exception $e) {
            return redirect('broadcast')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    public function resend($id)
    {
        //
        try {
            $user = \Auth::user();
            $organizationId=$user->organization_id;
            if(!is_numeric($id)){
                return redirect('broadcast')->with('error', trans('messages.INVALID_BROADCAST'));
            }

            $broadcastDetails = Broadcast::findOrfail($id);
            /*echo '<pre>';
            print_r($broadcastDetails);die;*/

            $i=0;
            /*echo '<pre>';
            print_r($buyerIds->toArray());die;*/

            if($broadcastDetails->receivers){
                $ids=$broadcastDetails->receivers;
            }else{
                $ids='';
            }

            if($ids){

                $broadcast = new Broadcast;
                $broadcast->type = $broadcastDetails->type;
                $broadcast->receivers = $ids;
                $broadcast->message = $broadcastDetails->message;
                $broadcast->is_active = 1;
                $broadcast->buyer_category = $broadcastDetails->buyer_category;
                $broadcast->organization_id = $organizationId;

                if($broadcast->save()){
                    $i++;
                }
            }
            /*echo '<pre>';
            print_r($buyers->toArray());die;*/
            
            
            
            if($i>0){
                return redirect('broadcast')->with('message',trans('messages.BROADCAST_RESEND_SUCCESS'));
            }else{
                return redirect('broadcast')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
            }
            
            
        } catch (Exception $e) {
            return redirect('broadcast')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('user::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('user::edit');
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
    public function destroy(Request $request)
    {
        $id = $request->input("id");
        $broadcast = Broadcast::findOrfail($id);


        if ($broadcast->delete()) {
            return array('broadcast' =>array(),'success'=>true,'msg'=>'success');
        }
        else{
            return array('success'=>false,'broadcast'=>array(),'msg'=>'fails');
        }
    }
}
