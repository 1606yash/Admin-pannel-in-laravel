<?php

namespace Modules\Cms\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cms\Entities\Page;
use Modules\Cms\Http\Requests\PageRequest;
use Auth;
use Image;
use DataTables;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {

        $userPermission = \Session::get('userPermission');

        if(!isset($userPermission[\Config::get('constants.FEATURES.PAGES')]))
            return view('error/403');

        $authUser = \Auth::user();

        $data = Page::from('pages as p')
                ->select('p.*')
                ->where('organization_id',$authUser->organization_id)
                ->orderby('p.id','desc')
                ->get();

        $pagesCount = 0;
        if(!empty($data->toArray())){
            $pagesCount = count($data);
        }

        if ($request->ajax()) {
            return Datatables::of($data)
                    ->addIndexColumn()
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

                    ->addColumn('visiblity', function ($row) {
                        if($row->visiblity == 1){
                            $visiblityValue = 'Public';
                        }else{
                            $visiblityValue = 'Private';
                        }

                        $value = 'badge badge-success';
                        $visiblity = '
                            <span class="tb-sub">
                                <span class="'.$value.'">
                                    '.$visiblityValue.'
                                </span>
                            </span>
                        ';
                        return $visiblity;
                    })
                    ->addColumn('updated_at', function ($row) {
                        return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->updated_at));
                    })
                    ->addColumn('action', function($row) use ($userPermission){
                           $edit = url('/').'/cms/pages/edit/'.$row->id;
                           $delete = url('/').'/cms/pages/delete/'.$row->id;
                           $confirm = '"Are you sure, you want to delete it?"';

                           if(isset($userPermission['pages']) && ($userPermission['pages']['edit_all'] || $userPermission['pages']['edit_own'])){
                                $editBtn = "<li>
                                            <a href='".$edit."'>
                                                <em class='icon ni ni-edit'></em> <span>Edit</span>
                                            </a>
                                        </li>";
                            }else{
                                $editBtn = '';
                            }

                            if(isset($userPermission['pages']) && ($userPermission['pages']['delete_all'] || $userPermission['pages']['delete_own'])){
                                $deleteBtn = "<li>
                                            <a href='#' data-id='".$row->id."' class='delete eg-swal-av3'>
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
                    ->rawColumns(['action','title','status','visiblity','updated_at'])
                    ->make(true);
        }
        return view('cms::pages/index')->with(compact('pagesCount'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('cms::pages/create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(PageRequest $request)
    {
        $user = Auth::user();

        $page = new Page();
        $page->organization_id = $user->organization_id;
        $page->title = $request->title;
        $page->slug = $request->slug;
        $page->meta_keywords = $request->MetaKeywords;
        $page->meta_description = $request->Metadescription;
        $page->status = $request->status;
        $page->visiblity = $request->visiblity;
        $page->description = $request->description;
        $page->image_link = $request->image_link;


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
            $extension  = $image1->getClientOriginalExtension();
            $image1Name = $image1Name.'_'.time().'.'.$extension;
            
            $destinationPath = public_path('uploads/pages');
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
            $image1_file = public_path('uploads/pages/'. $image1Name);

            $page->file = $image1Name;
            $page->original_name = $image1NameWithExt;
        }
        if($page->save()){
            return redirect('cms/pages')->with('message', trans('messages.PAGE_ADDED'));
        }else{
            return redirect('cms/pages')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
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
        $page = Page::findorfail($id);
        return view('cms::pages/create')->with(compact('page'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $page = Page::findorfail($id);
        $page->title = $request->title;
        $page->slug = $request->slug;
        $page->meta_keywords = $request->MetaKeywords;
        $page->meta_description = $request->Metadescription;
        $page->status = $request->status;
        $page->visiblity = $request->visiblity;
        $page->description = $request->description;
        $page->image_link = $request->image_link;


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
            $extension  = $image1->getClientOriginalExtension();
            $image1Name = $image1Name.'_'.time().'.'.$extension;
            
            $destinationPath = public_path('uploads/pages');
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
            $image1_file = public_path('uploads/pages/'. $image1Name);

            $page->file = $image1Name;
            $page->original_name = $image1NameWithExt;
        }
        if($page->save()){
            return redirect('cms/pages')->with('message', trans('messages.PAGE_UPDATED'));
        }else{
            return redirect('cms/pages')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request,$id)
    {
        $item = Page::findOrfail($id);

        if ($item->delete()) {
            return redirect('cms/pages')->with('message', trans('messages.PAGE_DELETED'));
        }else{
            return redirect('cms/pages')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }
}
