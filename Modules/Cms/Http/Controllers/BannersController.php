<?php

namespace Modules\Cms\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cms\Entities\Banner;
use Modules\Cms\Http\Requests\BannerRequest;
use Auth;
use Image;
use DataTables;

class BannersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $userPermission = \Session::get('userPermission');

        if(!isset($userPermission[\Config::get('constants.FEATURES.BANNERS')]))
            return view('error/403');

        $authUser = \Auth::user();

        $data = Banner::from('banners as b')
                ->select('b.*')
                ->where('organization_id',$authUser->organization_id)
                ->orderby('b.id','desc')
                ->get();

        $bannersCount = 0;
        if(!empty($data->toArray())){
            $bannersCount = count($data);
        }

        if ($request->ajax()) {
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('title', function($row){
                            if(!is_null($row->file)){
                                $file = public_path('uploads/banners/') . $row->file;
                            }
                            
                            $title = '<div class="nk-tb-col tb-col-sm">
                                <span class="tb-product">';

                            if(!is_null($row->file) && file_exists($file)){
                                $title .= '<img src="'.url('uploads/banners/'.$row->file).'" alt="" class="thumb">';
                            }

                            $title .='<span class="title">'.$row->title.'</span>
                                </span>
                            </div>';
                            return $title;
                    })
                    ->addColumn('status', function ($row) {
                        if($row->status == 1){
                            $statusValue = 'Active';
                        }else{
                            $statusValue = 'Inactive';
                        }

                        $value = ($row->status == '1') ? 'badge badge-success' : 'badge badge-danger';
                        $status = '
                            <span class="tb-sub">
                                <span class="'.$value.'">
                                    '.$statusValue.'
                                </span>
                            </span>
                        ';
                        return $status;
                    })
                    ->addColumn('action', function($row) use ($userPermission){
                           $edit = url('/').'/cms/banners/edit/'.$row->id;
                           $delete = url('/').'/cms/banners/delete/'.$row->id;
                           $confirm = '"Are you sure, you want to delete it?"';
                           if(isset($userPermission['banners']) && ($userPermission['banners']['edit_all'] || $userPermission['banners']['edit_own'])){
                                $editBtn = "<li>
                                            <a href='".$edit."'>
                                                <em class='icon ni ni-edit'></em> <span>Edit</span>
                                            </a>
                                        </li>";
                            }else{
                                $editBtn = '';
                            }

                            if(isset($userPermission['banners']) && ($userPermission['banners']['delete_all'] || $userPermission['banners']['delete_own'])){
                                $deleteBtn = "<li>
                                            <a href='".$delete."' onclick='return confirm(".$confirm.")'  class='delete'>
                                                <em class='icon ni ni-trash'></em> <span>Delete</span>
                                            </a>
                                        </li>"; 
                            }else{
                                $deleteBtn = '';
                            }
                           $btn = "
                                    <ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>
                                        ".$editBtn."
                                        ".$deleteBtn."
                                    </ul></div></div></li></ul>
                                ";
                            return $btn;
                    })
                    ->rawColumns(['action','title','status'])
                    ->make(true);
        }

        return view('cms::banners/index')->with(compact('bannersCount'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('cms::banners/create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(BannerRequest $request)
    {
        $user = Auth::user();


        $banner = new Banner();
        $banner->organization_id = $user->organization_id;
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->link = $request->exists("link") ? $request->input("link") : Null;
        $banner->target = $request->exists("target") ? $request->input("target") : '_self';


        if($request->exists('status')){
            $banner->status = 1;
        }else{
            $banner->status = 0;
        }


        if ($request->hasFile('banner')) {

            $image1 = $request->file('banner');
            $image1NameWithExt = $image1->getClientOriginalName();
            list($image1_width,$image1_height)=getimagesize($image1);
            // Get file path
            $originalName = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
            $image1Name = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
            // Remove unwanted characters
            $image1Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image1Name);
            $image1Name = preg_replace("/\s+/", '-', $image1Name);

            // Get the original image extension
            $extension  = $image1->getClientOriginalExtension();
            $image1Name = $image1Name.'_'.time().'.'.$extension;
            
            $destinationPath = public_path('uploads/banners');
            if($image1_width > 1450){
                $image1_canvas = Image::canvas(1450, 439);
                $image1_image = Image::make($image1->getRealPath())->resize(1450, 439, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $image1_canvas->insert($image1_image, 'center');
                $image1_canvas->save($destinationPath.'/'.$image1Name,80);
            }else{
                $image1->move($destinationPath, $image1Name);
            }
            $image1_file = public_path('uploads/banners/'. $image1Name);

            $banner->file = $image1Name;
            $banner->original_name = $image1NameWithExt;
        }
        if($banner->save()){
            return redirect('cms/banners')->with('message', trans('messages.BANNER_ADDED'));
        }else{
            return redirect('cms/banners')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('cms::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Request $request, $id)
    {
        $banner = Banner::findorfail($id);
        return view('cms::banners/create')->with(compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::findorfail($id);
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->link = $request->exists("link") ? $request->input("link") : Null;
        $banner->target = $request->exists("target") ? $request->input("target") : '_self';

        if($request->exists('status')){
            $banner->status = 1;
        }else{
            $banner->status = 0;
        }


        if ($request->hasFile('banner')) {

            $image1 = $request->file('banner');
            $image1NameWithExt = $image1->getClientOriginalName();
            list($image1_width,$image1_height)=getimagesize($image1);
            // Get file path
            $originalName = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
            $image1Name = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
            // Remove unwanted characters
            $image1Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image1Name);
            $image1Name = preg_replace("/\s+/", '-', $image1Name);

            // Get the original image extension
            $extension  = $image1->getClientOriginalExtension();
            $image1Name = $image1Name.'_'.time().'.'.$extension;
            
            $destinationPath = public_path('uploads/banners');
            if($image1_width > 1450){
                $image1_canvas = Image::canvas(1450, 439);
                $image1_image = Image::make($image1->getRealPath())->resize(1450, 439, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $image1_canvas->insert($image1_image, 'center');
                $image1_canvas->save($destinationPath.'/'.$image1Name,80);
            }else{
                $image1->move($destinationPath, $image1Name);
            }
            $image1_file = public_path('uploads/banners/'. $image1Name);

            $banner->file = $image1Name;
            $banner->original_name = $image1NameWithExt;
        }
        if($banner->save()){
            return redirect('cms/banners')->with('message', trans('messages.BANNER_UPDATED'));
        }else{
            return redirect('cms/banners')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request,$id)
    {
        $item = Banner::findOrfail($id);

        if ($item->delete()) {
            return redirect('cms/banners')->with('message', trans('messages.BANNER_DELETED'));
        }else{
            return redirect('cms/banners')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }
}
