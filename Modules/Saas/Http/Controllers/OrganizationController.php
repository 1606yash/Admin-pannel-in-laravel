<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Entities\State;
use Modules\User\Entities\District;
use Modules\User\Entities\City;
use Modules\Saas\Entities\DefaultSettings;
use Modules\Saas\Entities\IndustryMaster;
use Modules\Saas\Entities\SegmentMaster;
use Modules\Saas\Entities\MasterCategory;
use Modules\Saas\Entities\MasterBrand;
use Modules\Saas\Entities\MasterModel;
use Modules\Saas\Entities\MasterProduct;
use Modules\Saas\Entities\MasterProductCategory;
use Modules\Saas\Entities\MasterProductBrand;
use Modules\Saas\Entities\MasterProductModel;
use Modules\Saas\Entities\MasterProductMedia;
use Modules\Saas\Entities\MasterProductSku;
use Modules\Saas\Entities\MasterManufacturer;
use Modules\Saas\Entities\Module;
use Modules\Administration\Entities\MasterNotificationTemplate;
use DB;
use Modules\User\Entities\User;
use Modules\Saas\Entities\Organization;
use Modules\Saas\Entities\Currency;
use Modules\User\Entities\Role;
use Modules\User\Entities\ModelRole;
use Illuminate\Support\Facades\Hash;
use Image;
use Auth;
use DataTables;
use URL;
use Illuminate\Support\Str;
use Modules\User\Entities\ModuleFeature;
use Modules\User\Entities\OrganizationPermission;

class OrganizationController extends Controller
{

    public function __construct() {

        /* Execute authentication filter before processing any request */
        $this->middleware('auth');

        if (\Auth::check()) {
            return redirect('/');
        }

        $this->middleware(function ($request, $next){
            $role =  \Session::get('role');
            if($role != \Config::get('constants.ROLES.SUPERUSER')){
                return redirect('error');
            }else{
                return $next($request);
            }
        });
    }
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {

        //$user = Auth::user();        
        $data = Organization::select('organizations.id','organizations.name','organizations.status','industry','organizations.created_at','organizations.mobile','u.name as contact_person')
                    ->join('users as u','u.organization_id','=','organizations.id')
                    ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    ->join('roles as r','r.id','=','mr.role_id')
                    ->whereNull('organizations.deleted_at')
                    ->where('r.name','seller')
                    ->where(function ($query) use ($request) {
                        if (!empty($request->toArray())) {
                            if(isset($request->name) && (!empty($request->name) || $request->name != 0)){
                                $query->where('organizations.name', 'like', '%' . $request->name . '%');
                            }

                            if(isset($request->contact_person) && (!empty($request->contact_person) || $request->contact_person != 0)){
                                $query->where('u.name', 'like', '%' . $request->contact_person . '%');
                                $query->orWhere('u.last_name', 'like', '%' . $request->contact_person . '%');
                            }

                            if(isset($request->contact_person) && (!empty($request->contact_person) || $request->contact_person != 0)){
                                $query->where('u.name',$request->input('contact_person'));
                            }

                            if (isset($request->dateFrom) || isset($request->dateTo)) {

                                if (isset($request->dateFrom) && isset($request->dateTo)) {

                                    $dateFrom =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateFrom"))->format('Y-m-d');
                                    $dateTo =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateTo"))->format('Y-m-d');

                                    $dateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
                                    $query->whereBetween('organizations.created_at', array($dateFrom, $dateTo));
                                } elseif (isset($request->dateFrom)) {
                                    $dateFrom =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateFrom"))->format('Y-m-d');
                                    $query->whereDate('organizations.created_at', '>=', $dateFrom);
                                } elseif (isset($request->dateTo)) {
                                    $dateTo =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateTo"))->format('Y-m-d');
                                    $query->whereDate('organizations.created_at', '<=', $dateTo);
                                }
                            }
                        }
                    })
                    ->orderBy('organizations.created_at','DESC')
                    ->get();
        if ($request->ajax()) {
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                                    $edit = URL::to('/').'/saas/organization/edit/'.$row->id;
                                    $btn = '';
                                    $btn .= "<ul class='nk-tb-actions gx-1'>";
                                    $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='".$edit."' class='btn btn-trigger btn-icon' data-toggle='tooltip' data-placement='top' title='Edit'>
                                                        <em class='icon ni ni-edit'></em>
                                                    </a>
                                                </li>";
                                    return $btn;
                                })
                    
                    ->addColumn('name', function ($row) {
                        $name ='<a href="javascript:void(0);" data-id='.$row->id.' class= "organizationDetails">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span class="tb-lead text-primary">'.$row->name.'<span class="dot dot-success d-md-none ml-1"></span></span>
                                            <span>'.$row->email.'</span>
                                        </div>
                                    </div>
                                </a>';
                        return $name;
                    })      
                    
                    ->addColumn('created_at', function ($row) {
                        return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->created_at));
                    })

                    ->addColumn('contact_person', function ($row) {
                        return $row->contact_person;
                    })

                    ->addColumn('status', function ($row) {
                        $value = ($row->status == 'active') ? 'badge badge-success' : 'badge badge-danger';
                        $status = '
                            <span class="tb-sub">
                                <span class="'.$value.'">
                                    '.ucfirst($row->status).'
                                </span>
                            </span>
                        ';
                        return $status;
                    })
                    ->rawColumns(['action','created_at','status','name','contact_person'])
                    ->make(true);
        }                           
        /*echo '<pre>';
        print_r($brands->toArray());die;*/
        return view('saas::organization/index',['organizations'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $states = State::orderby('name','asc')->get();
        $currencies = Currency::orderby('name','asc')->get();
        return view('saas::organization/create',['states' => $states,'currencies' => $currencies]);
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function product(Request $request)
    {
        $user = Auth::user();

        $brands = MasterBrand::where('status','active')->whereNull('deleted_at')->get();
        $categories = MasterCategory::where('status','active')->whereNull('deleted_at')->get();
        $models = MasterModel::where('status','active')->whereNull('deleted_at')->get();

        $data = MasterProduct::select('master_products.type as type','master_products.id as id','master_products.name as name','media.file as file',DB::raw('group_concat(cat.name) as categories'),'sku.sale_price as price','master_products.status as status','master_products.created_at as created_at','brand.name as brand')
                    ->join('master_category_product as pro_cat','pro_cat.product_id','=','master_products.id')
                    ->join('master_categories as cat','cat.id','=','pro_cat.category_id')
                    ->join('master_product_model as pro_mod','pro_mod.product_id','=','master_products.id')
                    ->join('master_models as mod','mod.id','=','pro_mod.model_id')
                    ->join('master_product_brand as pro_brand','pro_brand.product_id','=','master_products.id')
                    ->join('master_brands as brand','brand.id','=','pro_brand.brand_id')
                    ->join('master_product_media as media','media.product_id','=','master_products.id')
                    ->join('master_product_sku as sku','sku.product_id','=','master_products.id')
                    ->whereNull('master_products.deleted_at')
                    ->where('media.type',1)
                    ->where(function ($query) use ($request) {
                        if (!empty($request->toArray())) {
                            if(isset($request->brand_id) && (!empty($request->brand_id) ) ){
                                $query->where('brand.id',$request->input('brand_id'));
                            }

                            if(isset($request->category_id) && (!empty($request->category_id) ) ){
                                $query->where('pro_cat.category_id',$request->input('category_id'));
                            }

                            if(isset($request->model) && (!empty($request->model) ) ){
                                $query->where('mod.id',$request->input('model'));
                            }

                            if(isset($request->productName) && (!empty($request->productName) || $request->productName != 0)){
                                $query->where('master_products.name', 'like', '%' . $request->productName . '%');
                            }
                        }
                    })
                    ->groupBy('master_products.id')
                    ->orderBy('master_products.id','DESC')
                    ->get();
 
            if ($request->ajax()) {
                
                return Datatables::of($data)
                        ->addIndexColumn()
                        ->addColumn('action', function($row){
                                        
                                    $edit = URL::to('/').'/saas/organization/ecommerce/products/edit/'.$row->id;
                                    $btn = '';
                                    $btn .= "<ul class='nk-tb-actions gx-1'>";
                                    $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='".$edit."' class='btn btn-trigger btn-icon' data-toggle='tooltip' data-placement='top' title='Edit'>
                                                        <em class='icon ni ni-edit'></em>
                                                    </a>
                                                </li>";
                                    $confirmMsg = 'Are you sure, you want to delete it?';
                                    $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='#' data-id='".$row->id."' class='btn btn-trigger btn-icon eg-swal-av3' data-toggle='tooltip' data-placement='top' title='Delete'>
                                                        <em class='icon ni ni-trash'></em>
                                                    </a>
                                                </li>";
                                       $btn .= "<li>
                                                    &nbsp;
                                                </li>
                                            </ul>";

                                    return $btn;
                                })
                    
                    ->addColumn('created_at', function ($row) {
                        return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->created_at));
                    })

                    
                    ->addColumn('name', function ($row) {

                        if($row->file !=''){
                            $img = URL::to('/').'/uploads/products/master/'.$row->file;
                        }else{
                            $img =URL::to('/').'/no1.jpg';
                        }

                        $name = '<img src='.$img.' alt="" class="thumb" width="50">
                        <span class="title">'. $row->name .'</span>';
                        return $name;
                    })      

                    ->addColumn('status', function ($row) {
                        $value = ($row->status == 'active') ? 'badge badge-success' : 'badge badge-danger';
                        $status = '
                            <span class="tb-sub">
                                <span class="'.$value.'">
                                    '.ucfirst($row->status).'
                                </span>
                            </span>
                        ';
                        return $status;
                    })
                    ->rawColumns(['action','created_at','status','name'])
                    ->make(true);
        }   

        return view('saas::organization/ecommerce/products/index',['products'=>$data,'brands'=>$brands,'categories'=>$categories,'models'=>$models]);
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function brands(Request $request)
    {
        $data = MasterBrand::select(DB::Raw('sum(case when (pro.brand_id!="") then 1 else 0 end) AS count'),'master_brands.file as file','master_brands.name as name','master_brands.id as id','master_brands.slug as slug','master_brands.is_featured as is_featured','master_brands.status as status','master_brands.created_at as created_at','master_brands.updated_at as updated_at')
            ->leftJoin('master_product_brand as pro','master_brands.id','=','pro.brand_id')
            ->whereNull('master_brands.deleted_at')
            ->where(function ($query) use ($request) {
                if (!empty($request->toArray())) {
                    if(isset($request->status) && (!empty($request->status) ) ){
                        $query->where('master_brands.status',$request->input('status'));
                    }

                    if(isset($request->name) && (!empty($request->name) || $request->name != 0)){
                        $query->where('master_brands.name',$request->input('name'));
                    }
                }
            })
            ->orderBy('master_brands.created_at','DESC')
            ->groupBy('master_brands.id')
            ->get();

        if ($request->ajax()) {
            /*echo '<pre>';
            print_r($data->toArray());die;*/
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                                    
                                    $btn = '';
                                    $btn .= "<ul class='nk-tb-actions gx-1'>";
                                    $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='#' data-id='".$row->id."' class='editItem btn btn-trigger btn-icon' data-target='addBrand' data-toggle='tooltip' data-placement='top' title='Edit'>
                                                        <em class='icon ni ni-edit'></em>
                                                    </a>
                                                </li>";
                                    $confirmMsg = 'Are you sure, you want to delete it?';
                                    $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='#' data-id='".$row->id."' class='btn btn-trigger btn-icon eg-swal-av3' data-toggle='tooltip' data-placement='top' title='Delete'>
                                                        <em class='icon ni ni-trash'></em>
                                                    </a>
                                                </li>";
                                       $btn .= "<li>
                                                    &nbsp;
                                                </li>
                                            </ul>";

                                    return $btn;
                                })
                    ->addColumn('created_at', function ($row) {
                        $created = date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->created_at));

                        return $created;
                    })

                    
                    ->addColumn('name', function ($row) {

                        if($row->file !=''){
                            $img = URL::to('/').'/uploads/brands/'.$row->file;
                        }else{
                            $img =URL::to('/').'/no1.jpg';
                        }

                        $name = '<img src='.$img.' alt="" class="thumb" width="50">
                        <span class="title">'. $row->name .'</span>';
                        return $name;
                    })      
                    
                    ->addColumn('updated_at', function ($row) {
                        $updated = date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->updated_at));

                        return $updated;
                    })
                    ->addColumn('products', function ($row) {
                        return $row->count;
                    })

                    ->addColumn('is_featured', function ($row) {
                        $class = ($row->is_featured == '1') ? 'icon ni ni-check' : '';
                        $isFeatured='<em class="'.$class.'"></em>';
                        return $isFeatured;
                    })

                    ->addColumn('status', function ($row) {
                        $value = ($row->status == 'active') ? 'badge badge-success' : 'badge badge-danger';
                        $status = '
                            <span class="tb-sub">
                                <span class="'.$value.'">
                                    '.ucfirst($row->status).'
                                </span>
                            </span>
                        ';
                        return $status;
                    })
                    ->rawColumns(['action','created_at','updated_at','status','products','name','is_featured'])
                    ->make(true);
        }                           

        return view('saas::organization/ecommerce/brands/index',['brands'=>$data]);
    }

    public function getBrand(Request $request)
    {
        $id = $request->input("id");
        $brand   =   MasterBrand::where('id',$id)->first();  
        if(!empty($brand->toArray())){
            return array('brand' => $brand,'success'=>true);
        }else{
            return array('success'=>false,'brand'=>array());
        }
    }

    public function getModule(Request $request)
    {
        $id = $request->input("id");
        $module   =   Module::where('id',$id)->first();  
        if(!empty($module->toArray())){
            return array('module' => $module,'success'=>true);
        }else{
            return array('success'=>false,'module'=>array());
        }
    }

    public function addBrand(Request $request)
    {
        try {

            $rules = array(
                'name' => 'required',     
                'slug' => 'required',
                // 'status' => 'required'
                //'image' => 'image|mimes:jpeg,png,jpg|max:5000',
                //'document' => 'mimes:pdf|max:5000'           
            );
            
            $validator = \Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                $messages = $validator->messages();
                return redirect('saas/organization/ecommerce/brands')->with('error', $messages);
            } else {
                

                if($request->input("id") && $request->input("id")!='0' && $request->input("id")!=''){
                    $brand = MasterBrand::find($request->input("id"));
                    $msg = 'Brand Updated Successfully.';
                }else{
                    $isExists = MasterBrand::where('id','!=',$request->id)->where('name',$request->input("name"))->get()->toArray();
                
                    if(!empty($isExists)){
                        return redirect('saas/organization/ecommerce/brands')->with('error', 'Brand title should be unique!');
                    }
                
                    $brand = new MasterBrand();
                    $msg = 'Brand Added Successfully.';
                }

                if ($request->hasFile('file')) {

                    $image1 = $request->file('file');
                    $image1NameWithExt = $image1->getClientOriginalName();
                    list($image1_width,$image1_height)=getimagesize($image1);
                    // Get file path
                    $originalName = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
                    $image1Name = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
                    // Remove unwanted characters
                    $image1Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image1Name);
                    $image1Name = preg_replace("/\s+/", '-', $image1Name);
                    // Get the original image extension
                    $extension = $image1->getClientOriginalExtension();
                    if($extension != 'jpg' && $extension != 'jpeg' && $extension != 'png'){
                        return redirect('saas/organization/ecommerce/brands')->with('error', 'Invalid image, Image should be a png,jpg or jpeg type');
                    }
                    $image1Name = 'img_1'.$image1Name.'_'.time().'.'.$extension;
                    
                    $destinationPath = public_path('uploads/brands');
                    if($image1_width > 800){
                        $image1_canvas = Image::canvas(800, 800);
                        $image1_image = Image::make($image1->getRealPath())->resize(800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $image1_canvas->insert($image1_image, 'center');
                        $image1_canvas->save($destinationPath.'/'.$image1Name,80);
                    }else{
                        $image1->move($destinationPath, $image1Name);
                    }
                    $image1_file = public_path('uploads/brands/'. $image1Name);

                    $brand->file = $image1Name;
                    $brand->original_name = $image1NameWithExt;
                }

                
                $brand->name = $request->input("name");
                $brand->slug = $request->exists("slug") ? $request->input("slug") : "";
                $brand->status = $request->input("status")=='1' ? 'active' : "inactive";
                $brand->is_featured = $request->input("is_featured")=='on' ? '1' : "0";

                if($brand->save()){
                    return redirect('saas/organization/ecommerce/brands')->with('message', $msg);
                }else{
                    return redirect('saas/organization/ecommerce/brands')->with('error', 'Something went wrong!');
                }
            }
            
        } catch (Exception $e) {
            return redirect('saas/organization/ecommerce/brands')->with('error', 'Something went wrong!');
        }
    }

    public function destroyBrand(Request $request)
    {
        //
        $id = $request->input("id");
        //$check   =   ProductBrand::where('brand_id',$id)->count();    
        $item = MasterBrand::findOrfail($id);

        $product_count = \DB::select("select count(*) as total_products from master_product_brand where brand_id = '".$id."'");

        if($product_count[0]->total_products > 0){
            return array('success'=>false,'brand'=>array(),'msg'=>"Can't delete brand! ".$product_count[0]->total_products." product(s) are associated with brand");
        }

        if ($item->delete()) {
            return array('brand' =>array(),'success'=>true,'msg'=>'success');
        }
        else{
            return array('success'=>false,'brand'=>array(),'msg'=>'fails');
        }
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function categories(Request $request)
    {
        $parentCtegories = MasterCategory::where('status','active')->whereNull('parent_id')->get();
        $data = MasterCategory::select(DB::Raw('sum(case when (pro.category_id!="") then 1 else 0 end) AS count'),'master_categories.name as name','master_categories.id as id','master_categories.slug as slug','parent.name as parent','master_categories.status as status','master_categories.created_at as created_at','master_categories.updated_at as updated_at')
            ->leftJoin('master_category_product as pro','master_categories.id','=','pro.category_id')
                    ->leftjoin('master_categories as parent','parent.id','=','master_categories.parent_id')
                    ->whereNull('master_categories.deleted_at')
                    ->where(function ($query) use ($request) {
                        if (!empty($request->toArray())) {
                            if(isset($request->parent_id) && (!empty($request->parent_id) || $request->type != 0)){
                                $query->where('parent.id',$request->input('parent_id'));
                            }

                            if(isset($request->name) && (!empty($request->name) || $request->name != 0)){
                                $query->where('master_categories.name',$request->input('name'));
                            }
                        }
                    })
                    ->groupBy('master_categories.id')
                    ->orderBy('master_categories.created_at','DESC')->get();
        //$data = MasterCategory::whereNull('deleted_at')->get();

        if ($request->ajax()) {
            /*echo '<pre>';
            print_r($data->toArray());die;*/
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                                    $btn = '';
                                    $btn .= "<ul class='nk-tb-actions gx-1'>";
                                    $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='#' data-id='".$row->id."' class='editItem btn btn-trigger btn-icon' data-target='addCategoryMaster' data-toggle='tooltip' data-placement='top' title='Edit'>
                                                        <em class='icon ni ni-edit'></em>
                                                    </a>
                                                </li>";
                                    $confirmMsg = 'Are you sure, you want to delete it?';
                                    $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='#' data-id='".$row->id."' class='btn btn-trigger btn-icon eg-swal-av3' data-toggle='tooltip' data-placement='top' title='Delete'>
                                                        <em class='icon ni ni-trash'></em>
                                                    </a>
                                                </li>";
                                       $btn .= "<li>
                                                    &nbsp;
                                                </li>
                                            </ul>";

                                    return $btn;
                                })
                    ->addColumn('created_at', function ($row) {
                        return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->created_at));
                    })
                    ->addColumn('updated_at', function ($row) {
                        return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->updated_at));
                    })
                    ->addColumn('products', function ($row) {
                        return $row->count;
                    })

                    ->addColumn('status', function ($row) {
                        $value = ($row->status == 'active') ? 'badge badge-success' : 'badge badge-danger';
                        $status = '
                            <span class="tb-sub">
                                <span class="'.$value.'">
                                    '.ucfirst($row->status).'
                                </span>
                            </span>
                        ';
                        return $status;
                    })
                    ->rawColumns(['action','created_at','updated_at','status','products'])
                    ->make(true);
        }
        /*echo '<pre>';
        print_r($categories->toArray());die;*/
        return view('saas::organization/ecommerce/categories/index',['categories'=>$data,'parentCtegories' => $parentCtegories,]);
    }

    public function addCategory(Request $request)
    {
        /*echo '<pre>';
        print_r(request()->all());die;*/
       
        try {

            $rules = array(
                'name' => 'required',
                // 'status' => 'required'         
            );
            
            $validator = \Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                $messages = $validator->messages();
                return redirect('saas/organization/ecommerce/categories')->with('error', $messages);
            } else {
                
                if($request->input("id") && $request->input("id")!='0' && $request->input("id")!=''){
                    $category = MasterCategory::find($request->input("id"));
                    $msg = 'Category Updated Successfully.';

                    $isExists = MasterCategory::where('id','!=',$request->input("id"))->where('name',$request->input("name"))->get()->toArray();
                    if(!empty($isExists)){
                        return redirect('saas/organization/ecommerce/categories')->with('error', 'Category title should be unique!');
                    }

                }else{
                    $isExists = MasterCategory::where('name',$request->input("name"))->get()->toArray();
                    if(!empty($isExists)){
                        return redirect('saas/organization/ecommerce/categories')->with('error', 'Category title should be unique!');
                    }
                
                    $category = new MasterCategory();
                    $msg = 'Category Added Successfully.';
                }

                $slug='';
                if($request->slug || $request->slug!=''){
                    if (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $request->slug)) {
                        return redirect('saas/organization/ecommerce/categories')->with('error', 'Allowed only letters,- or _ in slug !');
                    }
                }

                if(!$request->slug || $request->slug==''){
                    $slug = Str::slug($request->name, "_");
                }

                $category->name = $request->input("name");
                $category->description = $request->exists("description") ? $request->input("description") : "";
                $category->parent_id = $request->exists("parent_id") ? $request->input("parent_id") : "";
                $category->slug = $slug;
                $category->status = $request->input("status")=='1' ? 'active' : "inactive";
               
                if($category->save()){
                    return redirect('saas/organization/ecommerce/categories')->with('message', $msg);
                }else{
                    return redirect('saas/organization/ecommerce/categories')->with('error', 'Something went wrong!');
                }
            }
            
        } catch (Exception $e) {
            return redirect('saas/organization/ecommerce/categories')->with('error', 'Something went wrong!');
        }

    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function modules(Request $request)
    {
        $data = Module::get();
        //$data = MasterCategory::whereNull('deleted_at')->get();

        if ($request->ajax()) {
            /*echo '<pre>';
            print_r($data->toArray());die;*/
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){

                                    $details=URL::to('/').'/saas/organization/modules/detail';
                                    
                                    $btn = '';
                                    $btn .= "<ul class='nk-tb-actions gx-1'>";
                                    $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='#' data-target='addModule' data-id='".$row->id."' class='editItem btn btn-trigger btn-icon' data-toggle='tooltip' data-placement='top' title='Edit'>
                                                        <em class='icon ni ni-edit'></em>
                                                    </a>
                                                </li>";
                                    $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='".$details."' class='btn btn-trigger btn-icon' data-toggle='tooltip' data-placement='top' title='View'>
                                        <em class='icon ni ni-eye'></em>
                                        </a>
                                                </li>";
                                       $btn .= "<li>
                                                    &nbsp;
                                                </li>
                                            </ul>";

                                    return $btn;
                                })


                    ->addColumn('name', function ($row) {
                        $details=URL::to('/').'/saas/organization/modules/detail';

                        $btn = '';
                        $btn .= "<a href='".$details."'>
                                    <div class='user-card'>
                                        <div class='user-info'>
                                            <span class'tb-lead text-primary'>".$row->name."</span>
                                        </div>
                                    </div>
                                </a>";

                        return $btn;
                    })

                    ->addColumn('status', function ($row) {
                        $value = ($row->status == 'active') ? 'badge badge-success' : 'badge badge-danger';
                        $status = '
                            <span class="tb-sub">
                                <span class="'.$value.'">
                                    '.ucfirst($row->status).'
                                </span>
                            </span>
                        ';
                        return $status;
                    })
                    ->rawColumns(['action','name','status'])
                    ->make(true);
        }
        /*echo '<pre>';
        print_r($categories->toArray());die;*/
        return view('saas::organization/modules/index',['modules'=>$data]);
    }

    public function updateModule(Request $request)
    {
        //
        try {
            DB::beginTransaction();
            /*echo '<pre>';
            print_r(request()->all());die;*/

            $description = $request->input('description');
            $module = Module::find($request->id);
            $module->description = $request->description;
            

                /*echo '<pre>';
                print_r($organization);die;*/

                if($module->save()){
                    DB::commit();                                
                    return redirect('saas/organization/modules')->with('message', 'Module updated successfully.');
                }else{
                    DB::rollback();
                    return redirect('saas/organization/modules')->with('error', 'Something went wrong!');
                }
            
        } catch (Exception $e) {
             DB::rollback();
            return redirect('saas/organization/modules')->with('error', 'Something went wrong!');
        }
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function manufacturer(Request $request)
    {
        $user = Auth::user();

        $data = MasterManufacturer::whereNull('master_manufacturers.deleted_at')
                    ->orderBy('master_manufacturers.id','DESC')
                    ->get();
         
        if ($request->ajax()) {
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                                    
                                    $btn = '';
                                    $btn .= "<ul class='nk-tb-actions gx-1'>";
                                    $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='#' data-id='".$row->id."' class='editItem btn btn-trigger btn-icon' data-target='addManufacturer' data-toggle='tooltip' data-placement='top' title='Edit'>
                                                        <em class='icon ni ni-edit'></em>
                                                    </a>
                                                </li>";
                                    $confirmMsg = 'Are you sure, you want to delete it?';
                                    $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='#' data-id='".$row->id."' class='btn btn-trigger btn-icon eg-swal-av3' data-toggle='tooltip' data-placement='top' title='Delete'>
                                                        <em class='icon ni ni-trash'></em>
                                                    </a>
                                                </li>";
                                       $btn .= "<li>
                                                    &nbsp;
                                                </li>
                                            </ul>";

                                    return $btn;
                                })
                    ->addColumn('created_at', function ($row) {
                        return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->created_at));
                    })

                    ->addColumn('updated_at', function ($row) {
                        return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->created_at));
                    })

                    ->addColumn('status', function ($row) {
                        $value = ($row->status == 'active') ? 'badge badge-success' : 'badge badge-danger';
                        $status = '
                            <span class="tb-sub">
                                <span class="'.$value.'">
                                    '.ucfirst($row->status).'
                                </span>
                            </span>
                        ';
                        return $status;
                    })
                    ->rawColumns(['action','created_at','updated_at','status'])
                    ->make(true);
        }
        return view('saas::organization/ecommerce/manufacturer/index',['manufacturers'=>$data]);
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function models(Request $request)
    {
        $user = Auth::user();
        $manufacturers = MasterManufacturer::where('status','active')
                    ->orderBy('id','DESC')
                    ->get();

        $data = MasterModel::select(DB::Raw('sum(case when (pro.model_id!="") then 1 else 0 end) AS count'),'master_models.name as name','master_models.id as id','master_models.slug as slug','master_models.is_featured as is_featured','master_models.status as status','master_models.created_at as created_at','master_models.updated_at as updated_at','manu.name as manufacturer')
            ->join('master_manufacturers as manu','master_models.manufacturer_id','=','manu.id')
            ->leftJoin('master_product_model as pro','master_models.id','=','pro.model_id')
                    ->whereNull('master_models.deleted_at')
                    ->groupBy('master_models.id')
                    ->get();

        
            if ($request->ajax()) {
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                                    $btn = '';
                                    $btn .= "<ul class='nk-tb-actions gx-1'>";
                                    $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='#' data-id='".$row->id."' class='editItem btn btn-trigger btn-icon' data-target='addModels' data-toggle='tooltip' data-placement='top' title='Edit'>
                                                        <em class='icon ni ni-edit'></em>
                                                    </a>
                                                </li>";
                                    $confirmMsg = 'Are you sure, you want to delete it?';
                                    $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='#' data-id='".$row->id."' class='btn btn-trigger btn-icon eg-swal-av3' data-toggle='tooltip' data-placement='top' title='Delete'>
                                                        <em class='icon ni ni-trash'></em>
                                                    </a>
                                                </li>";
                                       $btn .= "<li>
                                                    &nbsp;
                                                </li>
                                            </ul>";

                                    return $btn;
                                })
                    ->addColumn('created_at', function ($row) {
                        return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->created_at));
                    })
                    ->addColumn('updated_at', function ($row) {
                        return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->updated_at));
                    })
                    ->addColumn('products', function ($row) {
                        return $row->count;
                    })

                    
                    ->addColumn('is_featured', function ($row) {
                       $class = ($row->is_featured == '1') ? 'icon ni ni-check' : '';

                       $isFeatured = '
                            <span class="tb-sub">
                                    <em class="'.$class.'"></em>
                            </span>
                        ';
                        return $isFeatured;
                    })

                    ->addColumn('status', function ($row) {
                        $value = ($row->status == 'active') ? 'badge badge-success' : 'badge badge-danger';
                        $status = '
                            <span class="tb-sub">
                                <span class="'.$value.'">
                                    '.ucfirst($row->status).'
                                </span>
                            </span>
                        ';
                        return $status;
                    })
                    ->rawColumns(['action','created_at','updated_at','status','products','is_featured'])
                    ->make(true);
        }
        return view('saas::organization/ecommerce/models/index',['ecommerceModels'=>$data,'manufacturers'=>$manufacturers]);
    }

    public function getModel(Request $request)
    {
        $id = $request->input("id");
        $model   =   MasterModel::where('id',$id)->first();  
        

        if(!empty($model->toArray())){
            return array('model' => $model,'success'=>true);
        }else{
            return array('success'=>false,'model'=>array());
        }
    }

    public function addModel(Request $request)
    {
        try {

            $rules = array(
                'name' => 'required',     
                'slug' => 'required',
                'manufacturer' => 'required'
                //'image' => 'image|mimes:jpeg,png,jpg|max:5000',
                //'document' => 'mimes:pdf|max:5000'           
            );
            
            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return redirect('saas/organization/ecommerce/models')->with('error', $messages);
            } else {

                if($request->input("id") && $request->input("id")!='0' && $request->input("id")!=''){
                    $ecommerceModel = MasterModel::find($request->input("id"));
                    $msg = 'Model Updated Successfully.';

                    $isExists = MasterModel::where('id','!=',$request->id)->where('name',$request->input("name"))->get()->toArray();
                
                    if(!empty($isExists)){
                        return redirect('saas/organization/ecommerce/models')->with('error', 'Model title should be unique!');
                    }

                }else{
                    $isExists = MasterModel::where('name',$request->input("name"))->get()->toArray();
                
                    if(!empty($isExists)){
                        return redirect('saas/organization/ecommerce/models')->with('error', 'Model title should be unique!');
                    }
                
                    $ecommerceModel = new MasterModel();
                    $msg = 'Model Added Successfully.';
                }

                if ($request->hasFile('file')) {

                    $image1 = $request->file('file');
                    $image1NameWithExt = $image1->getClientOriginalName();
                    list($image1_width,$image1_height)=getimagesize($image1);
                    // Get file path
                    $originalName = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
                    $image1Name = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
                    // Remove unwanted characters
                    $image1Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image1Name);
                    $image1Name = preg_replace("/\s+/", '-', $image1Name);
                    // Get the original image extension
                    $extension = $image1->getClientOriginalExtension();
                    $image1Name = 'img_1'.$image1Name.'_'.time().'.'.$extension;
                    
                    $destinationPath = public_path('uploads/model');
                    if($image1_width > 800){
                        $image1_canvas = Image::canvas(800, 800);
                        $image1_image = Image::make($image1->getRealPath())->resize(800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $image1_canvas->insert($image1_image, 'center');
                        $image1_canvas->save($destinationPath.'/'.$image1Name,80);
                    }else{
                        $image1->move($destinationPath, $image1Name);
                    }
                    $image1_file = public_path('uploads/model/'. $image1Name);

                    $ecommerceModel->file = $image1Name;
                    $ecommerceModel->original_name = $image1NameWithExt;
                }
                
               
                $ecommerceModel->name = $request->input("name");
                $ecommerceModel->manufacturer_id = $request->input("manufacturer");
                $ecommerceModel->slug = $request->exists("slug") ? $request->input("slug") : "";
                $ecommerceModel->status = $request->input("status")=='1' ? 'active' : "inactive";
                $ecommerceModel->is_featured = $request->input("is_featured")=='on' ? '1' : "0";

                if($ecommerceModel->save()){
                    return redirect('saas/organization/ecommerce/models')->with('message', $msg);
                }else{
                    return redirect('saas/organization/ecommerce/models')->with('error', 'Something went wrong!');
                }
            }
            
        } catch (Exception $e) {
            return redirect('saas/organization/ecommerce/models')->with('error', 'Something went wrong!');
        }
    }

    public function getManufacturer(Request $request)
    {
        $id = $request->input("id");
        $manufacturer   =   MasterManufacturer::where('id',$id)->first();  
        if(!empty($manufacturer->toArray())){
            return array('manufacturer' => $manufacturer,'success'=>true);
        }else{
            return array('success'=>false,'manufacturer'=>array());
        }
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function detail()
    {
        return view('saas::organization/modules/detail');
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */

    public function addProduct()
    {
        $user = Auth::user();

        $brands = MasterBrand::where('status','active')->whereNull('deleted_at')->get();
        $categories = MasterCategory::where('status','active')->whereNull('deleted_at')->get();
        $models = MasterModel::where('status','active')->whereNull('deleted_at')->get();
        $manufacturers = MasterManufacturer::where('status','active')->whereNull('deleted_at')->get();
        return view('saas::organization/ecommerce/products/add',['brands'=>$brands,'categories'=>$categories,'models'=>$models,'manufacturers'=>$manufacturers]);
    }


    public function editProduct(Request $request,$id)
    {
        $user = Auth::user();

        $product = MasterProduct::from('master_products as p')
            ->select('p.id','p.name','p.slug','p.status','p.manufacturer_id','p.is_featured','p.is_to_be_promoted','cat.category_id','brand.brand_id',
            'sku.regular_price','sku.sale_price','sku.code','sku.moq','p.description','sku.inventory_value',
                \DB::raw('group_concat(DISTINCT mod.name) as models')
            )
            ->leftjoin('master_product_sku as sku','p.id','=','sku.product_id')
            ->leftjoin('master_category_product as cat','p.id','=','cat.product_id')
            ->leftjoin('master_product_brand as brand','p.id','=','brand.product_id')
            // ->leftjoin('master_product_model as model','p.id','=','model.product_id')
            ->leftJoin('master_product_model as pro_mod','pro_mod.product_id','=','p.id')
            ->leftJoin('master_models as mod','mod.id','=','pro_mod.model_id')
            ->where('p.id',$id)
            ->groupBy('p.id')
            ->first();

        $brands = MasterBrand::where('status','active')->whereNull('deleted_at')->get();
        $categories = MasterCategory::where('status','active')->whereNull('deleted_at')->get();
        $models = MasterModel::where('status','active')->whereNull('deleted_at')->get();
        $manufacturers = MasterManufacturer::where('status','active')->whereNull('deleted_at')->get();

        $proMediaData = MasterProductMedia::where('product_id',$id)->get();  
        $imgUrl=URL::to('/')."/uploads/products/master/";

        return view('saas::organization/ecommerce/products/add',['brands'=>$brands,'categories'=>$categories,'models'=>$models,'manufacturers'=>$manufacturers,'product'=>$product,'media'=>$proMediaData,'imgUrl'=>$imgUrl]);
    }

    public function getManuModels($manuId)
    {
        $models   =   MasterModel::where('manufacturer_id',$manuId)->get();  
        if(!empty($models->toArray())){
            return $arrayName = array('models' => $models);
        }else{
            return false;
        }
    }

    public function createProduct(Request $request)
    {
       
        try {
            DB::beginTransaction();
            
            $slug='';
            if($request->slug || $request->slug!=''){
                if (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $request->slug)) {
                    return redirect('saas/organization/ecommerce/products/add')->with('error', 'Allowed only letters,- or _ in slug !');
                }
            }
            if(!$request->slug || $request->slug==''){
                $slug = Str::slug($request->name, "_");
            }


                        
            $product = new MasterProduct();
            $product->name  = $request->exists("name") ? $request->input("name") : "";
            $product->type  = $request->exists("type") ? $request->input("type") : "";
            $product->manufacturer_id  = $request->exists("manufacturer") ? $request->input("manufacturer") : "";
            $product->slug  = $slug!='' ? $slug : $request->slug;
            $product->description  = $request->exists("description") ? $request->input("description") : "";
            $product->status  = $request->input("status")=='active' ? 'active' : "inactive";
            $product->is_featured  = $request->input("is_featured")=='0' ? '0' : "1";
            $product->is_to_be_promoted  = $request->input("is_to_be_promoted")=='0' ? '0' : "1";

            if($product->save()){
                $productSku = new MasterProductSku();
                $productSku->regular_price = $request->exists("regular_price") ? $request->input("regular_price") : "";
                $productSku->sale_price = $request->exists("sale_price") ? $request->input("sale_price") : "";
                $productSku->code = $request->exists("sku") ? $request->input("sku") : "";
                $productSku->inventory = $request->exists("inventory") ? $request->input("inventory") : "";
                $productSku->inventory_value = $request->exists("inventory_value") ? $request->input("inventory_value") : "";
                $productSku->moq = $request->exists("moq") ? $request->input("moq") : "";
                $productSku->product_id = $product->id;
                $productSku->status = 'active';

                if(!empty($request->input("categories"))){
                    foreach($request->input("categories") as $key => $value){
                        $productCat = new MasterProductCategory();
                        $productCat->product_id = $product->id;
                        $productCat->category_id = $value;
                        $productCat->save();
                    }
                }

                if(!empty($request->input("brands"))){
                    foreach($request->input("brands") as $key => $value){
                        $productBrand = new MasterProductBrand();
                        $productBrand->product_id = $product->id;
                        $productBrand->brand_id = $value;
                        $productBrand->save();
                    }
                }

                if(!empty($request->input("models"))){
                    foreach($request->input("models") as $key => $value){
                        $productModel = new MasterProductModel();
                        $productModel->product_id = $product->id;
                        $productModel->model_id = $value;
                        $productModel->save();
                    }
                }



                if ($request->hasFile('main_image')) {


                    $file = $request->file('main_image');
                    $filenameWithExt = $file->getClientOriginalName();

                    list($width,$height)=getimagesize($file);

                    // Get file path
                    $originalName = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);


                    // Remove unwanted characters
                    $filename = preg_replace("/[^A-Za-z0-9 ]/", '', $filename);
                    $filename = preg_replace("/\s+/", '-', $filename);
                    // Get the original image extension
                    $destinationPath = public_path("uploads/products/master");


                    $extension = $file->getClientOriginalExtension();
                    $filename = $filename.'_'.time().'.'.$extension;
                    $fullFilename = 'full_'.$filename;
                    $mobileThumbnail = 'mobile_thumbnail_'.$filename;
                    $desktopThumbnail = 'desktop_thumbnail_'.$filename;
                    
                    $thumb_canvas = Image::canvas(80, 80);
                    $thumb_img = Image::make($file->getRealPath())->resize(80, 80, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $thumb_canvas->insert($thumb_img, 'center');
                    $thumb_canvas->save($destinationPath.'/'.$mobileThumbnail,80);

                    // $thumb_img = Image::make($file->getRealPath())->resize(80, 80);
                    // $thumb_img->save($destinationPath.'/'.$mobileThumbnail,80);

                    $desktop_thumb_canvas = Image::canvas(200, 200);
                    $desktop_thumb_img = Image::make($file->getRealPath())->resize(200, 200, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $desktop_thumb_canvas->insert($desktop_thumb_img, 'center');
                    $desktop_thumb_canvas->save($destinationPath.'/'.$desktopThumbnail,80);

                    // $desktop_thumb_img = Image::make($file->getRealPath())->resize(200, 200);
                    // $desktop_thumb_img->save($destinationPath.'/'.$desktopThumbnail,80);

                    $main_canvas = Image::canvas(800, 800);
                    $main_image = Image::make($file->getRealPath())->resize(800, 800, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $main_canvas->insert($main_image, 'center');
                    $main_canvas->save($destinationPath.'/'.$fullFilename,80);

                    $productM1 = new MasterProductMedia();
                    $productM1->product_id = $product->id;
                    $productM1->file = $fullFilename;
                    $productM1->original_name = $filenameWithExt;
                    $productM1->attachment_type = 1;
                    $productM1->type = 1;
                    $productM1->save();

                    $productM2 = new MasterProductMedia();
                    $productM2->product_id = $product->id;
                    $productM2->file = $mobileThumbnail;
                    $productM2->original_name = $filenameWithExt;
                    $productM2->attachment_type = 1;
                    $productM2->type = 3;
                    $productM2->save();



                    $productM2 = new MasterProductMedia();
                    $productM2->product_id = $product->id;
                    $productM2->file = $desktopThumbnail;
                    $productM2->original_name = $filenameWithExt;
                    $productM2->attachment_type = 1;
                    $productM2->type = 4;
                    $productM2->save();

                    
                }



                
                if ($request->hasFile('product_image_1')) {
                    $image1 = $request->file('product_image_1');

                    $image1NameWithExt = $image1->getClientOriginalName();

                    list($image1_width,$image1_height)=getimagesize($image1);

                    // Get file path
                    $originalName = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
                    $image1Name = pathinfo($image1NameWithExt, PATHINFO_FILENAME);

                    // Remove unwanted characters
                    $image1Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image1Name);
                    $image1Name = preg_replace("/\s+/", '-', $image1Name);
                    // Get the original image extension
                    $extension = $image1->getClientOriginalExtension();
                    $image1Name = 'img_1'.$image1Name.'_'.time().'.'.$extension;
               
                    $destinationPath = public_path("uploads/products/master");

                    if($image1_width > 800){
                        $image1_canvas = Image::canvas(800, 800);
                        $image1_image = Image::make($image1->getRealPath())->resize(800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $image1_canvas->insert($image1_image, 'center');
                        $image1_canvas->save($destinationPath.'/'.$image1Name,80);
                    }else{
                        $image1->move($destinationPath, $image1Name);
                    }

                    //$image1_file = public_path('media/normal_images/'. $image1Name);

                    $productM4 = new MasterProductMedia();
                    $productM4->product_id = $product->id;
                    $productM4->file = $image1Name;
                    $productM4->original_name = $image1NameWithExt;
                    $productM4->attachment_type = 1;
                    $productM4->type = 2;
                    $productM4->save();
                }

                if ($request->hasFile('product_image_2')) {
                    $image2 = $request->file('product_image_2');
                    $image2NameWithExt = $image2->getClientOriginalName();
                    list($image2_width,$image2_height)=getimagesize($image2);
                    // Get file path
                    $originalName = pathinfo($image2NameWithExt, PATHINFO_FILENAME);
                    $image2Name = pathinfo($image2NameWithExt, PATHINFO_FILENAME);

                    // Remove unwanted characters
                    $image2Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image2Name);
                    $image2Name = preg_replace("/\s+/", '-', $image2Name);
                    // Get the original image extension
                    $extension = $image2->getClientOriginalExtension();
                    $image2Name = $image2Name.'_'.time().'.'.$extension;
               
                    $destinationPath = public_path("uploads/products/master");
                    if($image2_width > 800){
                        $image2_image = Image::make($image2->getRealPath())->resize(800, 800);

                        $image2_canvas = Image::canvas(800, 800);
                        $image2_image = Image::make($image2->getRealPath())->resize(800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $image2_canvas->insert($image2_image, 'center');
                        $image2_canvas->save($destinationPath.'/'.$image2Name,80);
                    }else{
                        $image2->move($destinationPath, $image2Name);
                    }
                    //$image2_file = public_path("uploads/products/$organizationId". $image2Name);

                    $productM5 = new MasterProductMedia();
                    $productM5->product_id = $product->id;
                    $productM5->file = $image2Name;
                    $productM5->original_name = $image2NameWithExt;
                    $productM5->attachment_type = 1;
                    $productM5->type = 2;
                    $productM5->save();
                }

                if ($request->hasFile('product_image_3')) {
                    $image3 = $request->file('product_image_3');
                    $image3NameWithExt = $image3->getClientOriginalName();
                    list($image3_width,$image3_height)=getimagesize($image3);
                    // Get file path
                    $originalName = pathinfo($image3NameWithExt, PATHINFO_FILENAME);
                    $image3Name = pathinfo($image3NameWithExt, PATHINFO_FILENAME);

                    // Remove unwanted characters
                    $image3Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image3Name);
                    $image3Name = preg_replace("/\s+/", '-', $image3Name);
                    // Get the original image extension
                    $extension = $image3->getClientOriginalExtension();
                    $image3Name = $image3Name.'_'.time().'.'.$extension;
               
                    $destinationPath = public_path("uploads/products/master");
                    if($image3_width > 800){
                        $image3_image = Image::make($image3->getRealPath())->resize(800, 800);

                        $image3_canvas = Image::canvas(800, 800);
                        $image3_image = Image::make($image3->getRealPath())->resize(800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $image3_canvas->insert($image3_image, 'center');
                        $image3_canvas->save($destinationPath.'/'.$image3Name,80);
                    }else{
                        $image3->move($destinationPath, $image3Name);
                    }
                    //$image2_file = public_path("uploads/products/$organizationId". $image2Name);

                    $productM6 = new MasterProductMedia();
                    $productM6->product_id = $product->id;
                    $productM6->file = $image3Name;
                    $productM6->original_name = $image3NameWithExt;
                    $productM6->attachment_type = 1;
                    $productM6->type = 2;
                    $productM6->save();
                }

                if ($request->hasFile('product_image_4')) {
                    $image4 = $request->file('product_image_4');
                    $image4NameWithExt = $image4->getClientOriginalName();
                    list($image4_width,$image4_height)=getimagesize($image4);
                    // Get file path
                    $originalName = pathinfo($image4NameWithExt, PATHINFO_FILENAME);
                    $image4Name = pathinfo($image4NameWithExt, PATHINFO_FILENAME);

                    // Remove unwanted characters
                    $image4Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image4Name);
                    $image4Name = preg_replace("/\s+/", '-', $image3Name);
                    // Get the original image extension
                    $extension = $image4->getClientOriginalExtension();
                    $image4Name = $image4Name.'_'.time().'.'.$extension;
               
                    $destinationPath = public_path("uploads/products/master");
                    if($image4_width > 800){
                        $image4_image = Image::make($image4->getRealPath())->resize(800, 800);

                        $image4_canvas = Image::canvas(800, 800);
                        $image4_image = Image::make($image4->getRealPath())->resize(800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $image4_canvas->insert($image4_image, 'center');
                        $image4_canvas->save($destinationPath.'/'.$image4Name,80);
                    }else{
                        $image4->move($destinationPath, $image4Name);
                    }
                    //$image2_file = public_path("uploads/products/$organizationId". $image2Name);

                    $productM7 = new MasterProductMedia();
                    $productM7->product_id = $product->id;
                    $productM7->file = $image4Name;
                    $productM7->original_name = $image4NameWithExt;
                    $productM7->attachment_type = 1;
                    $productM7->type = 2;
                    $productM7->save();
                }

                

                
                if($productSku->save()){
                    DB::commit();
                    return redirect('saas/organization/ecommerce/products')->with('message', 'Product added successfully.');
                }else{
                    DB::rollback();
                    return redirect('saas/organization/ecommerce/products/add')->with('error', 'Something went wrong!');
                }
                
            }else{
                DB::rollback();
                return redirect('saas/organization/ecommerce/products/add')->with('error', 'Something went wrong!');
            }
           
        } catch (\Exception $e) {
           /*echo '<pre>';
           print_r($e->getMessage());die;*/
            return redirect('saas/organization/ecommerce/products/add')->with('error', 'Exception- '.$e->getMessage());

        }
        return view('ecommerce::create');
    }

    public function updateProduct(Request $request,$productId)
    {
        try {
            DB::beginTransaction();
            
            $slug='';
            if($request->slug || $request->slug!=''){
                if (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $request->slug)) {
                    return redirect('saas/organization/ecommerce/products/add')->with('error', 'Allowed only letters,- or _ in slug !');
                }
            }
            if(!$request->slug || $request->slug==''){
                $slug = Str::slug($request->name, "_");
            }
                        
            $product = MasterProduct::find($productId);;
            $product->name  = $request->exists("name") ? $request->input("name") : "";
            $product->type  = $request->exists("type") ? $request->input("type") : "";
            $product->manufacturer_id  = $request->exists("manufacturer") ? $request->input("manufacturer") : "";
            $product->slug  = $slug!='' ? $slug : $request->slug;
            $product->description  = $request->exists("description") ? $request->input("description") : "";
            $product->status  = $request->input("status")=='active' ? 'active' : "inactive";
            $product->is_featured  = $request->input("is_featured")=='0' ? '0' : "1";
            $product->is_to_be_promoted  = $request->input("is_to_be_promoted")=='0' ? '0' : "1";

            if($product->save()){


                $updateSkuData= array(
                                    'regular_price'=>$request->regular_price,
                                    'sale_price'=>$request->sale_price,
                                    'code'=>$request->sku,
                                    'inventory'=>$request->inventory,
                                    'inventory_value'=>$request->inventory_value,
                                    'moq'=>$request->moq,
                                    'status'=>$request->status
                                );

                $updateSku=MasterProductSku::where('product_id',$productId)->update($updateSkuData);

                if(!empty($request->input("categories"))){
                    $deleteCat = MasterProductCategory::where('product_id',$productId)->delete();
                    foreach($request->input("categories") as $key => $value){
                        $productCat = new MasterProductCategory();
                        $productCat->product_id = $productId;
                        $productCat->category_id = $value;
                        $productCat->save();
                    }
                }

                if(!empty($request->input("brands"))){
                    $deleteBrand = MasterProductBrand::where('product_id',$productId)->delete();
                    foreach($request->input("brands") as $key => $value){
                        $productBrand = new MasterProductBrand();
                        $productBrand->product_id = $productId;
                        $productBrand->brand_id = $value;
                        $productBrand->save();
                    }
                }

                if(!empty($request->input("models"))){
                    $deleteModel = MasterProductModel::where('product_id',$productId)->delete();
                    foreach($request->input("models") as $key => $value){
                        $productModel = new MasterProductModel();
                        $productModel->product_id = $productId;
                        $productModel->model_id = $value;
                        $productModel->save();
                    }
                }

                if ($request->hasFile('main_image')) {

                    $file = $request->file('main_image');
                    $filenameWithExt = $file->getClientOriginalName();

                    list($width,$height)=getimagesize($file);

                    // Get file path
                    $originalName = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

                    // Remove unwanted characters
                    $filename = preg_replace("/[^A-Za-z0-9 ]/", '', $filename);
                    $filename = preg_replace("/\s+/", '-', $filename);
                    // Get the original image extension
                    $destinationPath = public_path("uploads/products/master");


                    $extension = $file->getClientOriginalExtension();
                    $filename = $filename.'_'.time().'.'.$extension;
                    $fullFilename = 'full_'.$filename;
                    $mobileThumbnail = 'mobile_thumbnail_'.$filename;
                    $desktopThumbnail = 'desktop_thumbnail_'.$filename;
                    
                    $thumb_canvas = Image::canvas(80, 80);
                    $thumb_img = Image::make($file->getRealPath())->resize(80, 80, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $thumb_canvas->insert($thumb_img, 'center');
                    $thumb_canvas->save($destinationPath.'/'.$mobileThumbnail,80);

                    // $thumb_img = Image::make($file->getRealPath())->resize(80, 80);
                    // $thumb_img->save($destinationPath.'/'.$mobileThumbnail,80);

                    $desktop_thumb_canvas = Image::canvas(200, 200);
                    $desktop_thumb_img = Image::make($file->getRealPath())->resize(200, 200, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $desktop_thumb_canvas->insert($desktop_thumb_img, 'center');
                    $desktop_thumb_canvas->save($destinationPath.'/'.$desktopThumbnail,80);

                    // $desktop_thumb_img = Image::make($file->getRealPath())->resize(200, 200);
                    // $desktop_thumb_img->save($destinationPath.'/'.$desktopThumbnail,80);

                    $main_canvas = Image::canvas(800, 800);
                    $main_image = Image::make($file->getRealPath())->resize(800, 800, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $main_canvas->insert($main_image, 'center');
                    $main_canvas->save($destinationPath.'/'.$fullFilename,80);


                    $deleteMainImg = MasterProductMedia::where('product_id',$productId)->where('type',1)->delete();
                    $deleteThumImg = MasterProductMedia::where('product_id',$productId)->where('type',3)->delete();
                    $deleteDtImg = MasterProductMedia::where('product_id',$productId)->where('type',4)->delete();

                    $productM1 = new MasterProductMedia();
                    $productM1->product_id = $product->id;
                    $productM1->file = $fullFilename;
                    $productM1->original_name = $filenameWithExt;
                    $productM1->attachment_type = 1;
                    $productM1->type = 1;
                    $productM1->save();

                    $productM2 = new MasterProductMedia();
                    $productM2->product_id = $product->id;
                    $productM2->file = $mobileThumbnail;
                    $productM2->original_name = $filenameWithExt;
                    $productM2->attachment_type = 1;
                    $productM2->type = 3;
                    $productM2->save();

                    $productM2 = new MasterProductMedia();
                    $productM2->product_id = $product->id;
                    $productM2->file = $desktopThumbnail;
                    $productM2->original_name = $filenameWithExt;
                    $productM2->attachment_type = 1;
                    $productM2->type = 4;
                    $productM2->save();

                    
                }

                
                if ($request->hasFile('product_image_1')) {
                    $image1 = $request->file('product_image_1');

                    $image1NameWithExt = $image1->getClientOriginalName();

                    list($image1_width,$image1_height)=getimagesize($image1);

                    // Get file path
                    $originalName = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
                    $image1Name = pathinfo($image1NameWithExt, PATHINFO_FILENAME);

                    // Remove unwanted characters
                    $image1Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image1Name);
                    $image1Name = preg_replace("/\s+/", '-', $image1Name);
                    // Get the original image extension
                    $extension = $image1->getClientOriginalExtension();
                    $image1Name = 'img_1'.$image1Name.'_'.time().'.'.$extension;
               
                    $destinationPath = public_path("uploads/products/master");

                    if($image1_width > 800){
                        $image1_canvas = Image::canvas(800, 800);
                        $image1_image = Image::make($image1->getRealPath())->resize(800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $image1_canvas->insert($image1_image, 'center');
                        $image1_canvas->save($destinationPath.'/'.$image1Name,80);
                    }else{
                        $image1->move($destinationPath, $image1Name);
                    }

                    //$image1_file = public_path('media/normal_images/'. $image1Name);

                    $productM4 = new MasterProductMedia();
                    $productM4->product_id = $product->id;
                    $productM4->file = $image1Name;
                    $productM4->original_name = $image1NameWithExt;
                    $productM4->attachment_type = 1;
                    $productM4->type = 2;
                    $productM4->save();
                }

                if ($request->hasFile('product_image_2')) {
                    $image2 = $request->file('product_image_2');
                    $image2NameWithExt = $image2->getClientOriginalName();
                    list($image2_width,$image2_height)=getimagesize($image2);
                    // Get file path
                    $originalName = pathinfo($image2NameWithExt, PATHINFO_FILENAME);
                    $image2Name = pathinfo($image2NameWithExt, PATHINFO_FILENAME);

                    // Remove unwanted characters
                    $image2Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image2Name);
                    $image2Name = preg_replace("/\s+/", '-', $image2Name);
                    // Get the original image extension
                    $extension = $image2->getClientOriginalExtension();
                    $image2Name = $image2Name.'_'.time().'.'.$extension;
               
                    $destinationPath = public_path("uploads/products/master");
                    if($image2_width > 800){
                        $image2_image = Image::make($image2->getRealPath())->resize(800, 800);

                        $image2_canvas = Image::canvas(800, 800);
                        $image2_image = Image::make($image2->getRealPath())->resize(800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $image2_canvas->insert($image2_image, 'center');
                        $image2_canvas->save($destinationPath.'/'.$image2Name,80);
                    }else{
                        $image2->move($destinationPath, $image2Name);
                    }
                    //$image2_file = public_path("uploads/products/$organizationId". $image2Name);

                    $productM5 = new MasterProductMedia();
                    $productM5->product_id = $product->id;
                    $productM5->file = $image2Name;
                    $productM5->original_name = $image2NameWithExt;
                    $productM5->attachment_type = 1;
                    $productM5->type = 2;
                    $productM5->save();
                }

                if ($request->hasFile('product_image_3')) {
                    $image3 = $request->file('product_image_3');
                    $image3NameWithExt = $image3->getClientOriginalName();
                    list($image3_width,$image3_height)=getimagesize($image3);
                    // Get file path
                    $originalName = pathinfo($image3NameWithExt, PATHINFO_FILENAME);
                    $image3Name = pathinfo($image3NameWithExt, PATHINFO_FILENAME);

                    // Remove unwanted characters
                    $image3Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image3Name);
                    $image3Name = preg_replace("/\s+/", '-', $image3Name);
                    // Get the original image extension
                    $extension = $image3->getClientOriginalExtension();
                    $image3Name = $image3Name.'_'.time().'.'.$extension;
               
                    $destinationPath = public_path("uploads/products/master");
                    if($image3_width > 800){
                        $image3_image = Image::make($image3->getRealPath())->resize(800, 800);

                        $image3_canvas = Image::canvas(800, 800);
                        $image3_image = Image::make($image3->getRealPath())->resize(800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $image3_canvas->insert($image3_image, 'center');
                        $image3_canvas->save($destinationPath.'/'.$image3Name,80);
                    }else{
                        $image3->move($destinationPath, $image3Name);
                    }
                    //$image2_file = public_path("uploads/products/$organizationId". $image2Name);

                    $productM6 = new MasterProductMedia();
                    $productM6->product_id = $product->id;
                    $productM6->file = $image3Name;
                    $productM6->original_name = $image3NameWithExt;
                    $productM6->attachment_type = 1;
                    $productM6->type = 2;
                    $productM6->save();
                }

                if ($request->hasFile('product_image_4')) {
                    $image4 = $request->file('product_image_4');
                    $image4NameWithExt = $image4->getClientOriginalName();
                    list($image4_width,$image4_height)=getimagesize($image4);
                    // Get file path
                    $originalName = pathinfo($image4NameWithExt, PATHINFO_FILENAME);
                    $image4Name = pathinfo($image4NameWithExt, PATHINFO_FILENAME);

                    // Remove unwanted characters
                    $image4Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image4Name);
                    $image4Name = preg_replace("/\s+/", '-', $image3Name);
                    // Get the original image extension
                    $extension = $image4->getClientOriginalExtension();
                    $image4Name = $image4Name.'_'.time().'.'.$extension;
               
                    $destinationPath = public_path("uploads/products/master");
                    if($image4_width > 800){
                        $image4_image = Image::make($image4->getRealPath())->resize(800, 800);

                        $image4_canvas = Image::canvas(800, 800);
                        $image4_image = Image::make($image4->getRealPath())->resize(800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $image4_canvas->insert($image4_image, 'center');
                        $image4_canvas->save($destinationPath.'/'.$image4Name,80);
                    }else{
                        $image4->move($destinationPath, $image4Name);
                    }
                    //$image2_file = public_path("uploads/products/$organizationId". $image2Name);

                    $productM7 = new MasterProductMedia();
                    $productM7->product_id = $product->id;
                    $productM7->file = $image4Name;
                    $productM7->original_name = $image4NameWithExt;
                    $productM7->attachment_type = 1;
                    $productM7->type = 2;
                    $productM7->save();
                }
                if($updateSku){
                    DB::commit();
                    return redirect('saas/organization/ecommerce/products')->with('message', 'Product updated successfully.');
                }else{
                    DB::rollback();
                    return redirect('saas/organization/ecommerce/products/add')->with('error', 'Something went wrong!');
                }
                
            }else{
                DB::rollback();
                return redirect('saas/organization/ecommerce/products/edit/'.$productId)->with('error', 'Something went wrong!');
            }
           
        } catch (\Exception $e) {
           /*echo '<pre>';
           print_r($e->getMessage());die;*/
            return redirect('saas/organization/ecommerce/products/add')->with('error', 'Exception- '.$e->getMessage());

        }
        return view('ecommerce::create');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function industryMaster(Request $request)
    {
        $data= IndustryMaster::select('industries.id','industries.slug','industries.type','industries.description','industries.status')->whereNull('industries.deleted_at')->orderBy('industries.id','DESC')->get();
            if ($request->ajax()) {
                return Datatables::of($data)
                        ->addIndexColumn()
                        ->addColumn('action', function($row){
                                        
                                        $btn = '';
                                        $btn .= "<ul class='nk-tb-actions gx-1'>";
                                        $btn .= "<li class='nk-tb-action-hidden'>
                                                        <a href='#' data-id='".$row->id."' class='editItem btn btn-trigger btn-icon' data-target='addIndustriesMaster' data-toggle='tooltip' data-placement='top' title='Edit'>
                                                            <em class='icon ni ni-edit'></em>
                                                        </a>
                                                    </li>";
                                        $confirmMsg = 'Are you sure, you want to delete it?';
                                        $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='#' data-id='".$row->id."' class='btn btn-trigger btn-icon eg-swal-av3' data-toggle='tooltip' data-placement='top' title='Delete'>
                                                        <em class='icon ni ni-trash'></em>
                                                    </a>
                                                </li>";

                                        return $btn;
                                    })
                        ->addColumn('status', function ($row) {
                            $value = ($row->status == 'active') ? 'badge badge-success' : 'badge badge-danger';
                            $status = '
                                <span class="tb-sub">
                                    <span class="'.$value.'">
                                        '.ucfirst($row->status).'
                                    </span>
                                </span>
                            ';
                            return $status;
                        })
                            
                        ->rawColumns(['action','status'])
                        ->make(true);
            } 

        return view('saas::organization/master-industry',['industry'=>$data]);
    }

    public function segmentMaster(Request $request)
    {
        $data= SegmentMaster::select('segments.id','segments.slug','segments.type','segments.description','segments.status')->whereNull('segments.deleted_at')->orderBy('segments.id','DESC')->get();
            if ($request->ajax()) {
                return Datatables::of($data)
                        ->addIndexColumn()
                        ->addColumn('action', function($row){
                                        
                                        $btn = '';
                                        $btn .= "<ul class='nk-tb-actions gx-1'>";
                                        $btn .= "<li class='nk-tb-action-hidden'>
                                                        <a href='#' data-id='".$row->id."' class='editItem btn btn-trigger btn-icon' data-target='addSegmentMaster' data-toggle='tooltip' data-placement='top' title='Edit'>
                                                            <em class='icon ni ni-edit'></em>
                                                        </a>
                                                    </li>";
                                        $confirmMsg = 'Are you sure, you want to delete it?';
                                        $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='#' data-id='".$row->id."' class='btn btn-trigger btn-icon eg-swal-av3' data-toggle='tooltip' data-placement='top' title='Delete'>
                                                        <em class='icon ni ni-trash'></em>
                                                    </a>
                                                </li>";
                                        return $btn;
                                    })
                        ->addColumn('status', function ($row) {
                            $value = ($row->status == 'active') ? 'badge badge-success' : 'badge badge-danger';
                            $status = '
                                <span class="tb-sub">
                                    <span class="'.$value.'">
                                        '.ucfirst($row->status).'
                                    </span>
                                </span>
                            ';
                            return $status;
                        })
                            
                        ->rawColumns(['action','status'])
                        ->make(true);
            }

        $industries = IndustryMaster::where('status','active')->get();

        return view('saas::organization/master-segment',['segments'=>$data,'industries'=>$industries]);
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function settings(Request $request)
    {

        if($request->category!=''){
            $data= DefaultSettings::select('default_settings.id','default_settings.label','default_settings.type','default_settings.code','default_settings.updated_at')->where('category',$request->category)->whereNull('default_settings.deleted_at')->orderBy('default_settings.id','DESC')->get();

            if ($request->ajax()) {

                return Datatables::of($data)
                        ->addIndexColumn()
                        ->addColumn('action', function($row){
                                        
                                        $btn = '';
                                        $btn .= "<ul class='nk-tb-actions gx-1'>";
                                        $btn .= "<li class='nk-tb-action-hidden'>
                                                        <a href='#' data-id='".$row->id."' class='editItem btn btn-trigger btn-icon' data-target='addSettingsMaster' data-toggle='tooltip' data-placement='top' title='Edit'>
                                                            <em class='icon ni ni-edit'></em>
                                                        </a>
                                                    </li>";
                                        $confirmMsg = 'Are you sure, you want to delete it?';
                                        $btn .= "<li class='nk-tb-action-hidden'>
                                                    <a href='#' data-id='".$row->id."' class='btn btn-trigger btn-icon eg-swal-av3' data-toggle='tooltip' data-placement='top' title='Delete'>
                                                        <em class='icon ni ni-trash'></em>
                                                    </a>
                                                </li>";
                                        $btn .= "<li>
                                                    &nbsp;
                                                </li>
                                            </ul>";

                                        return $btn;
                                    })
                            
                        
                        ->addColumn('updated_at', function ($row) {
                            return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->updated_at));
                        })
                        
                        ->rawColumns(['action','updated_at'])
                        ->make(true);
            } 
        }


        return view('saas::organization/settings/index');
    }

    public function getSetting(Request $request)
    {
        if($request->id!=''){
            $id = $request->id;
            $defSetting   =   DefaultSettings::where('id',$id)->first();  
            
            if(!empty($defSetting->toArray())){
                return array('setting' => $defSetting,'success'=>true);
            }else{
                return array('success'=>false,'setting'=>array());
            }
        }
    }

    public function getIndustryMaster(Request $request)
    {
        if($request->id!=''){
            $id = $request->id;
            $industry   =   IndustryMaster::where('id',$id)->first();  
            
            if(!empty($industry->toArray())){
                return array('industry' => $industry,'success'=>true);
            }else{
                return array('success'=>false,'industry'=>array());
            }
        }
    }

    public function getSegmentMaster(Request $request)
    {
        if($request->id!=''){
            $id = $request->id;
            $segment   =   SegmentMaster::where('id',$id)->first();  
            
            if(!empty($segment->toArray())){
                return array('segment' => $segment,'success'=>true);
            }else{
                return array('success'=>false,'segment'=>array());
            }
        }
    }    

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
        try {

            $rules = array(
                'gstNumber' => 'required|min:15|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                'mobile' => 'required|max:10',
            );

            $user = \Auth::user();
            $organizationId=$user->organization_id;
            $validator = \Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                $messages = $validator->messages();
                return \Redirect::to('saas/organization/add')->withErrors($validator);
            }

            DB::beginTransaction();
            $organizationName = $request->input('organizationName');
            $userEmailExists =  User::select('*')
                                ->where('email',$request->email)
                                ->orWhere('phone_number',$request->mobile)
                                ->get()
                                ->toArray();
            $isOrgNameExists =  Organization::
                                where('name',$organizationName)
                                ->orWhere('gst',$request->gstNumber)
                                ->orWhere('mobile',$request->mobile)
                                ->get()
                                ->toArray();

            if(!empty($isOrgNameExists)){
                DB::rollback();

                if($isOrgNameExists[0]['gst'] == $request->gstNumber){
                    return redirect('saas/organization')->with('error', 'GST number already exists!');
                }
                elseif($isOrgNameExists[0]['mobile'] == $request->mobile){
                    return redirect('saas/organization')->with('error', 'Mobile number already exists!');
                }else{
                    return redirect('saas/organization')->with('error', 'Organization name already exists!');
                }

            }else if(!empty($userEmailExists)){
                DB::rollback();
                if($userEmailExists[0]['phone_number'] == $request->mobile){
                    return redirect('saas/organization')->with('error', 'Contact person mobile number already exists!');
                }else{
                    return redirect('saas/organization')->with('error', 'Contact person email already exists!');
                }
            } else if($request->input("password") != $request->input("cn_password")){
                DB::rollback();
                return redirect('saas/organization')->with('error', 'Password and confirm password should be same!');
            }else{

                $organization = new Organization();
                $organization->name = $request->organizationName;
                $organization->currency = $request->currency;
                $organization->industry = $request->industry;
                $organization->country = 103;
                $organization->mobile = $request->mobile;
                $organization->gst = $request->gstNumber;
                $organization->status = $request->input("status")=='active' ? 'active' : "inactive";

                if($organization->save()){
                    $organizationId = $organization->id;

                    $role1 = new Role();
                    $role1->name = 'buyer';
                    $role1->label = 'Buyer';
                    $role1->is_default = 1;
                    $role1->guard_name = 'web';
                    $role1->organization_id = $organizationId;

                    $role2 = new Role();
                    $role2->name = 'sales_person';
                    $role2->label = 'Sales Person';
                    $role2->is_default = 1;
                    $role2->guard_name = 'web';
                    $role2->organization_id = $organizationId;

                    $role3 = new Role();
                    $role3->name = 'seller';
                    $role3->label = 'Seller';
                    $role3->is_default = 1;
                    $role3->guard_name = 'web';
                    $role3->organization_id = $organizationId;
    
                    $role1->save();
                    $role2->save();
                    

                    if($role3->save()){
                        $roleId = $role3->id;

                        $features = ModuleFeature::all();
                        $permissions = array();
                        foreach ($features as $key => $feature) {
                            $permissions[] = array(
                                'role_id' => $roleId,
                                'feature_id' => $feature->id,
                                'read_own' => 1,
                                'read_all' => 1,
                                'edit_own' => 1,
                                'edit_all' => 1,
                                'delete_own' => 1,
                                'delete_all' => 1
                            );
                        }

                        if(!empty($permissions)){
                            $addPermissions = OrganizationPermission::insert($permissions);
                        }

                        $userExists =  User::select('*')->where('email',$request->username)->get()->toArray();
                        if(!empty($userExists)){
                            DB::rollback();
                            return redirect('saas/organization')->with('error', 'User email is already exists!');
                        }else{
                            $user = new User();
                            $user->password         = Hash::make($request->input("password"));
                            $user->organization_id  = $organizationId;
                            $user->name = $request->exists("contactPerson") ? $request->input("contactPerson") : "";
                            $user->email  = $request->exists("email") ? $request->input("email") : "";
                            $user->phone_number  = $request->exists("mobile") ? $request->input("mobile") : "";

                            $user->address1 = $request->exists("Street1") ? $request->input("Street1") : "";
                            $user->address2 = $request->exists("Street2") ? $request->input("Street2") : "";
                            $user->country  = 103;
                            $user->state    = $request->exists("state") ? $request->input("state") : "";
                            $user->city     = $request->exists("city") ? $request->input("city") : "";
                            $user->district = $request->exists("district") ? $request->input("district") : "";
                            $user->pincode  = $request->exists("pincode") ? $request->input("pincode") : "";
                            
                            if($user->save()){

                                $copyNotificationTemplates = MasterNotificationTemplate::query()
                                    ->each(function ($template) use ($organizationId) {
                                    $newTemplate = $template->replicate();
                                    $newTemplate->setTable('notification_templates');
                                    $newTemplate->organization_id = $organizationId;
                                    $newTemplate->save();
                                });

                                $copySettings = DefaultSettings::query()
                                    ->each(function ($setting)  use ($organizationId) {
                                    $newSetting = $setting->replicate();
                                    $newSetting->setTable('settings');
                                    if($newSetting->type == 'SELECT'){
                                        $newSetting->value = '';
                                    }
                                    $newSetting->default_options = $setting->value;
                                    $newSetting->organization_id = $organizationId;
                                    $newSetting->save();
                                });

                                $modelRole = new ModelRole();
                                $modelRole->role_id = $roleId;
                                $modelRole->model_type = 'Modules\User\Entities\User';
                                $modelRole->model_id = $user->id;
                                
                                if($modelRole->save()){
                                    DB::commit();                                
                                    return redirect('saas/organization')->with('message', 'Organization added successfully.');
                                }else{
                                    DB::rollback();
                                    return redirect('saas/organization')->with('error', 'Something went wrong!');
                                }
                                

                                
                            }else{
                                DB::rollback();
                                return redirect('saas/organization')->with('error', 'Something went wrong!');
                            }
                        }
                    }else{
                        DB::rollback();
                        return redirect('saas/organization')->with('error', 'Something went wrong!');
                    }

                }else{
                    DB::rollback();
                    return redirect('saas/organization')->with('error', 'Something went wrong!');
                }
            }
            
        } catch (Exception $e) {
             DB::rollback();
            return redirect('admin/add')->with('error', 'Something went wrong!');
        }
    }

    public function addSetting(Request $request)
    {
        //
        try {
            DB::beginTransaction();
           /* echo '<pre>';
            print_r(request()->all());die;*/
            if(!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $request->code)) {
                    return redirect('saas/organization/settings')->with('error', 'Allowed only letters, numbers and _ in code !');
            }

            if($request->id!='' && $request->id!=0 && is_numeric($request->id)){
                
                $isExists =  DefaultSettings::select('*')->where('id','!=',$request->id)->where('category',$request->category)->where('code',$request->code)->get()->toArray();
                
                if(!empty($isExists)){
                    DB::rollback();
                    return redirect('saas/organization/settings')->with('error', 'Setting code already exists!');
                }

                $defSettings = DefaultSettings::find($request->id);
                $msg='Setting updated successfully.';
                $defSettings->updated_at = date('Y-m-d H:i:s');


            }else{

                $isExists =  DefaultSettings::select('*')->where('category',$request->category)->where('code',$request->code)->get()->toArray();
                
                if(!empty($isExists)){
                    DB::rollback();
                    return redirect('saas/organization/settings')->with('error', 'Setting code already exists!');
                }

                $defSettings = new DefaultSettings();
                $msg='Setting added successfully.';

            }
            

            
                if($request->code==1){
                    $defSettings->type='TEXT';
                }else if($request->code==2){
                    $defSettings->type='TEXTAREA';
                }else if($request->code==3){
                    $defSettings->type='BOOLEAN';
                }else if($request->code==4){
                    $defSettings->type='NUMBER';
                }else if($request->code==5){
                    $defSettings->type='DATE';
                }else{
                    $defSettings->type="SELECT";
                }

                
                $defSettings->code = $request->code;
                $defSettings->category = $request->category;
                $defSettings->label = $request->label;
                $defSettings->description = $request->description;
                if($request->exists('selectOptions')){
                    $defSettings->value = $request->selectOptions;
                }

                if($defSettings->save()){
                    DB::commit();                                
                    return redirect('saas/organization/settings')->with('message', $msg);
                }else{
                    DB::rollback();
                    return redirect('saas/organization/settings')->with('error', 'Something went wrong!');
                }
            
            
        } catch (Exception $e) {
             DB::rollback();
            return redirect('saas/organization/settings')->with('error', 'Something went wrong!');
        }
    }

    public function addIndustry(Request $request)
    {
        //$user = Auth::user();
        try {

            $rules = array(
                'industryType' => 'required' 
            );
            
            $validator = \Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                $messages = $validator->messages();
                return redirect('saas/organization/industries')->with('error', $messages);
            } else {


                if($request->input("id") && $request->input("id")!='0' && $request->input("id")!=''){
                    $industry = IndustryMaster::find($request->input("id"));
                    $msg = 'Industry Updated Successfully.';
                }else{
                    $isExists = IndustryMaster::where('type',$request->input("industryType"))->get()->toArray();
                
                    if(!empty($isExists)){
                        return redirect('saas/organization/industries')->with('error', 'Industry type should be unique!');
                    }
                
                    $industry = new IndustryMaster();
                    $msg = 'Industry Added Successfully.';
                }

                if($request->slug || $request->slug!=''){
                    if (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $request->slug)) {
                        return redirect('saas/organization/industries')->with('error', 'Allowed only letters,- or _ in slug !');
                    }
                }

                if(!$request->slug || $request->slug==''){
                    $slug='';
                    $slug = Str::slug($request->industryType, "_");
                }else{
                    $slug=$request->slug;
                }

                $industry->type = $request->input("industryType");
                $industry->slug = $slug ;
                $industry->description = $request->exists("description") ? $request->input("description") : "";
                $industry->status = $request->input("status")=='1' ? 'active' : "inactive";
                
                if($industry->save()){
                    return redirect('saas/organization/industries')->with('message', $msg);
                }else{
                    return redirect('saas/organization/industries')->with('error', 'Something went wrong!');
                }
            }
            
        } catch (Exception $e) {
            return redirect('saas/organization/industries')->with('error', 'Something went wrong!');
        }
    }

    public function addSegment(Request $request)
    {
        //$user = Auth::user();
        try {

            $rules = array(
                'segmentType' => 'required' 
            );
            
            $validator = \Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                $messages = $validator->messages();
                return redirect('saas/organization/segments')->with('error', $messages);
            } else {


                if($request->input("id") && $request->input("id")!='0' && $request->input("id")!=''){
                    $segment = SegmentMaster::find($request->input("id"));
                    $msg = 'Segment Updated Successfully.';
                }else{
                    $isExists = SegmentMaster::where('type',$request->input("segmentType"))->get()->toArray();
                
                    if(!empty($isExists)){
                        return redirect('saas/organization/segments')->with('error', 'Industry type should be unique!');
                    }
                
                    $segment = new SegmentMaster();
                    $msg = 'Segment Added Successfully.';
                }

                if($request->slug || $request->slug!=''){
                    if (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $request->slug)) {
                        return redirect('saas/organization/segments')->with('error', 'Allowed only letters,- or _ in slug !');
                    }
                }

                if(!$request->slug || $request->slug==''){
                    $slug='';
                    $slug = Str::slug($request->segmentType, "_");
                }else{
                    $slug=$request->slug;
                }

                $segment->type = $request->input("segmentType");
                $segment->industry = $request->input("industry");
                $segment->slug = $slug ;
                $segment->description = $request->exists("description") ? $request->input("description") : "";
                $segment->status = $request->input("status")=='1' ? 'active' : "inactive";
                
                if($segment->save()){
                    return redirect('saas/organization/segments')->with('message', $msg);
                }else{
                    return redirect('saas/organization/segments')->with('error', 'Something went wrong!');
                }
            }
            
        } catch (Exception $e) {
            return redirect('saas/organization/segments')->with('error', 'Something went wrong!');
        }
    }

    public function addManufacturer(Request $request)
    {
        try {

            $rules = array(
                'name' => 'required'
            );
            $user = Auth::user();
            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return redirect('saas/organization/ecommerce/manufacturer')->with('error', $messages);
            } else {
                

                if($request->input("id") && $request->input("id")!='0' && $request->input("id")!=''){
                    $manufacturer = MasterManufacturer::find($request->input("id"));
                    $slug=$request->input("slug");
                    $msg = 'Manufacturer Updated Successfully.';

                    $isExists = MasterManufacturer::where('id','!=',$request->id)->where('name',$request->input("name"))->get()->toArray();
                
                    if(!empty($isExists)){
                        return redirect('saas/organization/ecommerce/manufacturer')->with('error', 'Manufacturer name should be unique!');
                    }

                }else{
                    $isExists = MasterManufacturer::where('name',$request->input("name"))->get()->toArray();
                
                    if(!empty($isExists)){
                        return redirect('saas/organization/ecommerce/manufacturer')->with('error', 'Manufacturer name should be unique!');
                    }

                    $slug='';
                    if($request->slug || $request->slug!=''){
                        if (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $request->slug)) {
                            return redirect('saas/organization/ecommerce/manufacturer')->with('error', 'Allowed only letters,- or _ in slug !');
                        }
                    }

                    if(!$request->slug || $request->slug==''){
                        $slug = Str::slug($request->name, "_");
                    }
                
                    $manufacturer = new MasterManufacturer();
                    $msg = 'Manufacturer Added Successfully.';
                }

                $manufacturer->name = $request->input("name");
                $manufacturer->slug = $slug;
                $manufacturer->status = $request->input("status")=='1' ? 'active' : "inactive";
                
                if($manufacturer->save()){
                    return redirect('saas/organization/ecommerce/manufacturer')->with('message', $msg);
                }else{
                    return redirect('saas/organization/ecommerce/manufacturer')->with('error', 'Something went wrong!');
                }
            }
            
        } catch (Exception $e) {
            return redirect('saas/organization/ecommerce/manufacturer')->with('error', 'Something went wrong!');
        }
    }

    public function massUpdate(Request $request)
    {

        try {
 
            if( empty($request->input("ids"))){
                return array('success'=>false,'item'=>array(),'msg'=>'Please select an item!');
            }

            if($request->input("status") == '0' ){
                return array('success'=>false,'item'=>array(),'msg'=>'Please select  bulk status!');
            }
            
            $i=0;    
            foreach($request->input("ids") as $key=>$value ){
                $ind = IndustryMaster::find($value);
                $ind->status = $request->input("status");
                $ind->save();
                $i++;
            }


            if($i>0){
                return array('success'=>true,'item' => array(),'msg'=>'true');
            }else{
                return array('success'=>false,'item'=>array(),'msg'=>'No update required');
            }
                            
        } catch (Exception $e) {
            
            return array('success'=>false,'item'=>array(),'msg'=>'Something went wrong!');
        }
    }

    public function massUpdateSegment(Request $request)
    {

        try {
 
            if( empty($request->input("ids"))){
                return array('success'=>false,'item'=>array(),'msg'=>'Please select an item!');
            }

            if($request->input("status") == '0' ){
                return array('success'=>false,'item'=>array(),'msg'=>'Please select  bulk status!');
            }
            
            $i=0;    
            foreach($request->input("ids") as $key=>$value ){
                $ind = SegmentMaster::find($value);
                $ind->status = $request->input("status");
                $ind->save();
                $i++;
            }


            if($i>0){
                return array('success'=>true,'item' => array(),'msg'=>'true');
            }else{
                return array('success'=>false,'item'=>array(),'msg'=>'No update required');
            }
                            
        } catch (Exception $e) {
            
            return array('success'=>false,'item'=>array(),'msg'=>'Something went wrong!');
        }
    }

    public function massUpdateBrand(Request $request)
    {

        try {
 
            if( empty($request->input("ids"))){
                return array('success'=>false,'item'=>array(),'msg'=>'Please select a brand!');
            }

            if($request->input("status") == '0' ){
                return array('success'=>false,'item'=>array(),'msg'=>'Please select  bulk status!');
            }
            
            $i=0;    
            foreach($request->input("ids") as $key=>$value ){
                $ind = MasterBrand::find($value);
                $ind->status = $request->input("status");
                $ind->save();
                $i++;
            }


            if($i>0){
                return array('success'=>true,'item' => array(),'msg'=>'true');
            }else{
                return array('success'=>false,'item'=>array(),'msg'=>'No update required');
            }
                            
        } catch (Exception $e) {
            
            return array('success'=>false,'item'=>array(),'msg'=>'Something went wrong!');
        }
    }

    public function massUpdateCategory(Request $request)
    {

        try {
 
            
            if( empty($request->input("ids"))){
                return array('success'=>false,'item'=>array(),'msg'=>'Please select an item!');
                
            }

            if($request->input("status") == '0' ){
                return array('success'=>false,'item'=>array(),'msg'=>'Please select bulk status!');
            }
            
            $i=0;    
            foreach($request->input("ids") as $key=>$value ){
                $cat = MasterCategory::find($value);
                $cat->status = $request->input("status");
                $cat->save();
                $i++;
            }


            if($i>0){
                return array('success'=>true,'item' => array(),'msg'=>'true');
            }else{
                return array('success'=>false,'item'=>array(),'msg'=>'No update required');
            }
                            
        } catch (Exception $e) {
            
            return array('success'=>false,'item'=>array(),'msg'=>'Something went wrong!');
        }
    }

    public function massUpdateModel(Request $request)
    {
        try {
            $user = Auth::user();
            if( empty($request->input("ids"))){
                return array('success'=>false,'item'=>array(),'msg'=>'Please select an item!');      
            }

            if($request->input("status") == '0' ){
                return array('success'=>false,'item'=>array(),'msg'=>'Please select bulk status!');
            }
            
            $i=0;    
            foreach($request->input("ids") as $key=>$value ){
                $model = MasterModel::find($value);
                $model->status = $request->input("status");
                $model->save();
                $i++;
            }
            if($i>0){
                return array('success'=>true,'item' => array(),'msg'=>'true');
            }else{
                return array('success'=>false,'item'=>array(),'msg'=>'No update required');
            }
                            
        } catch (Exception $e) {
            
            return array('success'=>false,'item'=>array(),'msg'=>'Something went wrong!');
        }
    }

    public function massUpdateManufacturer(Request $request)
    {
        try {
            if( empty($request->input("ids"))){
                return array('success'=>false,'item'=>array(),'msg'=>'Please select an item!');
            }

            if($request->input("status") == '0' ){
                return array('success'=>false,'item'=>array(),'msg'=>'Please select  bulk status!');
            }
            
            $i=0;    
            foreach($request->input("ids") as $key=>$value ){
                $brand = MasterManufacturer::find($value);
                $brand->status = $request->input("status");
                $brand->save();
                $i++;
            }
           if($i>0){
                return array('success'=>true,'item' => array(),'msg'=>'true');
            }else{
                return array('success'=>false,'item'=>array(),'msg'=>'No update required');
            }
                            
        } catch (Exception $e) {
            
            return array('success'=>false,'item'=>array(),'msg'=>'Something went wrong!');
        }
    }

    public function getCategory(Request $request)
    {
        $id = $request->input("id");
        $category   =   MasterCategory::where('id',$id)->first();  
        

        if(!empty($category->toArray())){
            return array('category' => $category,'success'=>true);
        }else{
            return array('success'=>false,'category'=>array());
        }
    }


    public function show(Request $request, $id)
    {
        $organization = Organization::select('organizations.id','organizations.name as organizationName','organizations.status','industry','organizations.created_at','organizations.mobile','organizations.street_1','organizations.pincode','organizations.gst','s.name as state','c.name as city','co.name as country','u.email',
            'u.name','u.last_name','u.phone_number'
            )
            ->join('users as u','u.organization_id','=','organizations.id')
            ->join('model_has_roles as mr','mr.model_id','=','u.id')
            ->join('roles as r','r.id','=','mr.role_id')
            ->leftJoin('states as s','s.id','=','organizations.state')
            ->leftJoin('cities as c','c.id','=','organizations.city')
            ->leftJoin('countries as co','co.id','=','organizations.country')
            ->whereNull('organizations.deleted_at')
            ->where('organizations.id',$id)
            ->where('r.name','seller')
            ->orderBy('organizations.created_at','DESC')
            ->first();

        if(!empty($organization->toArray())){
            return array('organization' => $organization,'success'=>true);
        }else{
            return array('success'=>false,'organization'=>array());
        }
    }

    public function edit($id)
    {   
        if($id == '' || !is_numeric($id)){
            return redirect('saas/organization')->with('error', 'Organization not found !');
        }
        $states = State::orderby('name','asc')->get();
        $organization = Organization::select('organizations.id','organizations.name','organizations.status','industry','organizations.created_at','organizations.mobile','u.name as contact_person','organizations.state','organizations.city','organizations.district','organizations.pincode','organizations.gst','u.email','organizations.street_1','organizations.street_2','organizations.pincode','organizations.currency')
                    ->join('users as u','u.organization_id','=','organizations.id')
                    ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    ->join('roles as r','r.id','=','mr.role_id')
                    ->where('organizations.id',$id)
                    ->first();
        
        $currencies = Currency::orderby('name','asc')->get();
        if(!$organization){
            return redirect('saas/organization')->with('error', 'Organization not found !');
        }

        return view('saas::organization/edit',['states' => $states,'organization'=>$organization,'currencies'=>$currencies]);
    }
/**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function module()
    {
        return view('saas::organization/module');
    }
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request)
    {
        //
        try {
            DB::beginTransaction();
            $rules = array(
                'gstNumber' => 'required|min:15|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/|unique:organizations,gst,' . $request->organization_id,
                'mobile' => 'required|max:10|unique:organizations,mobile,' . $request->organization_id
            );

            $user = \Auth::user();
            $organizationId=$user->organization_id;
            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->messages();
                return \Redirect::to('saas/organization/add')->withErrors($validator);
            }


            $organizationName = $request->input('organizationName');
            
            $userEmailExists =  User::select('*')->where('email',$request->email)->where('organization_id','!=',$request->organization_id)->get()->toArray();

            $isOrgNameExists =  Organization::where('id','!=',$request->organization_id)
                                ->where(function ($query) use ($organizationName,$request) {
                                    $query->where('name',$organizationName);
                                    $query->orWhere('gst',$request->gstNumber);
                                    $query->orWhere('mobile',$request->mobile);
                                })
                                ->get()
                                ->toArray();


            if(!empty($isOrgNameExists)){
                DB::rollback();
                if($isOrgNameExists[0]['gst'] == $request->gstNumber){
                    return redirect('saas/organization')->with('error', 'GST number already exists!');
                } elseif($isOrgNameExists[0]['mobile'] == $request->mobile){
                    return redirect('saas/organization')->with('error', 'Mobile number already exists!');
                }else{
                    return redirect('saas/organization')->with('error', 'Organization name already exists!');
                }
            }else if(!empty($userEmailExists)){
                DB::rollback();
                return redirect('saas/organization')->with('error', 'Organization should have unique email !');
            }else{

                $organization = Organization::find($request->organization_id);
                $organization->name = $request->organizationName;
                $organization->currency = $request->currency;
                $organization->city = $request->city;
                $organization->district = $request->district;
                $organization->state = $request->state;
                $organization->industry = $request->industry;
                //$organization->country = 103;
                $organization->mobile = $request->mobile;
                $organization->gst = $request->gstNumber;
                $organization->street_1 = $request->Street1;
                $organization->street_2 = $request->Street2;
                $organization->pincode = $request->pincode;
                $organization->status = $request->input("status")=='active' ? 'active' : "inactive";

                /*echo '<pre>';
                print_r($organization);die;*/

                if($organization->save()){

                    DB::commit();                                
                    return redirect('saas/organization')->with('message', 'Organization updated successfully.');
                    /*$sellerData =   User::select('users.id')
                                ->join('model_has_roles as mr','mr.model_id','=','users.id')
                                ->join('roles as r','r.id','=','mr.role_id')
                                ->where('users.organization_id',$request->organization_id)
                                ->where('r.name','seller')
                                ->first();

                    $seller = User::findOrfail($sellerData->id);
                    $seller->email = $request->email;
                    $seller->name  = $request->contactPerson;

                    if($seller->save()){
                        DB::commit();                                
                        return redirect('saas/organization')->with('message', 'Organization updated successfully.');
                    }else{
                        DB::rollback();
                        return redirect('saas/organization')->with('error', 'Something went wrong!');
                    }*/
                }else{
                    DB::rollback();
                    return redirect('saas/organization')->with('error', 'Something went wrong!');
                }
            }
            
        } catch (Exception $e) {
             DB::rollback();
            return redirect('admin/add')->with('error', 'Something went wrong!');
        }
    }

    public function getCitiesByState($state_id)
    {
        $cities   =   City::where('state_id',$state_id)->orderby('name','asc')->get();  
        if(!empty($cities->toArray())){
            return $arrayName = array('cities' => $cities);
        }else{
            return false;
        }
    }

    public function destroySegment(Request $request)
    {
        $id = $request->input("id");
        $item = SegmentMaster::findOrfail($id);

        if ($item->delete()) {
            return array('category' =>array(),'success'=>true,'msg'=>'success');
        }
        else{
            return array('success'=>false,'category'=>array(),'msg'=>'fails');
        }
    }

    public function destroyCategory(Request $request)
    {
        $id = $request->input("id");

        $product_count = \DB::select("select count(*) as total_products from master_category_product where category_id = '".$id."'");

        if($product_count[0]->total_products > 0){
            return array('success'=>false,'category'=>array(),'msg'=>"Can't delete category! ".$product_count[0]->total_products." product(s) are associated with category");
        }
        
        $item = MasterCategory::findOrfail($id);

        if ($item->delete()) {
            return array('category' =>array(),'success'=>true,'msg'=>'success');
        }
        else{
            return array('success'=>false,'category'=>array(),'msg'=>'fails');
        }
    }

    public function destroyIndustry(Request $request)
    {
        $id = $request->input("id");
        $item = IndustryMaster::findOrfail($id);

        if ($item->delete()) {
            return array('model' =>array(),'success'=>true,'msg'=>'success');
        }
        else{
            return array('success'=>false,'model'=>array(),'msg'=>'fails');
        }
    }

    public function destroyModel(Request $request)
    {
        $id = $request->input("id");

        $product_count = \DB::select("select count(*) as total_products from master_product_model where model_id = '".$id."'");

        if($product_count[0]->total_products > 0){
            return array('success'=>false,'model'=>array(),'msg'=>"Can't delete model! ".$product_count[0]->total_products." product(s) are associated with model");
        }

        $item = MasterModel::findOrfail($id);

        if ($item->delete()) {
            return array('model' =>array(),'success'=>true,'msg'=>'success');
        }
        else{
            return array('success'=>false,'model'=>array(),'msg'=>'fails');
        }
    }

    public function destroyManufacturer(Request $request)
    {
        $id = $request->input("id");

        $product_count = \DB::select("select count(*) as total_products from master_products where manufacturer_id = '".$id."'");

        if($product_count[0]->total_products > 0){
            return array('success'=>false,'manufacturer'=>array(),'msg'=>"Can't delete manufacturer! ".$product_count[0]->total_products." product(s) are associated with manufacturer");
        }

        $item = MasterManufacturer::findOrfail($id);

        if ($item->delete()) {
            return array('manufacturer' =>array(),'success'=>true,'msg'=>'success');
        }
        else{
            return array('success'=>false,'manufacturer'=>array(),'msg'=>'fails');
        }
    }

    public function destroySettings(Request $request)
    {
        $id = $request->input("id");
        $item = DefaultSettings::findOrfail($id);

        if ($item->delete()) {
            return array('settings' =>array(),'success'=>true,'msg'=>'success');
        }
        else{
            return array('success'=>false,'settings'=>array(),'msg'=>'fails');
        }
    }

    public function destroyProduct(Request $request)
    {

        $id = $request->input("id");
        $product = MasterProduct::findOrfail($id);
        $deleteProBrand=MasterProductBrand::where('product_id',$id)->delete();
        $deleteProMedia=MasterProductMedia::where('product_id',$id)->delete();
        $deleteProModel=MasterProductModel::where('product_id',$id)->delete();
        $deleteProCat=MasterProductCategory::where('product_id',$id)->delete();

        if ($product->delete()) {
            return array('product' =>array(),'success'=>true,'msg'=>'success');
        }
        else{
            return array('success'=>false,'product'=>array(),'msg'=>'fails');
        }
    }
}
