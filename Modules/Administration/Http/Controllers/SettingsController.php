<?php

namespace Modules\Administration\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Entities\DefaultSettings;
use Modules\Saas\Entities\Settings;
use Modules\Saas\Entities\HomeSetting;
use Auth;
use Image;
class SettingsController extends Controller
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
    public function index()
    {

        $userPermission = \Session::get('userPermission');
        if(!isset($userPermission[\Config::get('constants.FEATURES.SETTINGS')]))
            return view('error/403');
        
        $user = Auth::user();
        $settings = Settings::where('organization_id',$user->organization_id)->get();
        $settingData = array();
        if(!empty($settings->toArray())){
            foreach ($settings as $key => $setting) {
                $settingData[$setting->category][] = $setting->toArray();
            }
        }

        $categories = array(); // Category::select('id','name')->where('status','active')->get();

        $homeSettings = HomeSetting::where('organization_id',$user->organization_id)
                        ->first();

        return view('administration::settings/index')->with(compact('settingData','homeSettings','categories'));
    }

    
    public function updateHomeSettings(Request $request){

        $user = Auth::user();

        $settings = HomeSetting::where('organization_id',$user->organization_id)->first();

        if(!$settings){
            $settings = new HomeSetting();
            $settings->organization_id           = $user->organization_id;
        }

        if(!empty($request->categories)){
            $categories = implode(',', $request->categories);
            $settings->categories           = $categories;
        }else{
            $settings->categories           = Null;
        }

        $settings->best_seller          = $request->exists("best_seller") ? 1 : 0;
        $settings->featured_product     = $request->exists("featured_products") ? 1 : 0;
        $settings->brands               = $request->exists("brands") ? 1 : 0;
        $settings->models               = $request->exists("models") ? 1 : 0;
        $settings->recommended_products = $request->exists("recommended_products") ? 1 : 0;
        $settings->new_arrivals         = $request->exists("new_arrivals") ? 1 : 0;
        $settings->inventory            = $request->exists("inventory") ? 1 : 0;
        $settings->segments            = $request->exists("segments") ? 1 : 0;

        if($settings->save()){
            return redirect('administration/settings')->with('message', trans('messages.SETTINGS_UPDATED'));
        }else{
            return redirect('administration/settings')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
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
        try {


            $setting = Settings::where('id',$request->id)->first();
            if($setting->type == 'TEXT' || $setting->type == 'NUMBER' || $setting->type == 'SELECT' || $setting->type == 'TEXTAREA'){
                $setting->value = $request->value;
            }
            elseif ($setting->type == 'BOOLEAN') {
                if($request->exists("value")){
                    $setting->value = 'true';
                }else{
                    $setting->value = 'false';
                }
            }
            elseif ($setting->type == 'FILE') {
                if ($request->hasFile('value')) {

                    $image1 = $request->file('value');
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
                    $image1Name = $image1Name.'_'.time().'.'.$extension;
                    
                    $destinationPath = public_path('uploads/settings');
                    $image1->move($destinationPath, $image1Name);
                    $setting->value = $image1Name;
                }
            }
            if($setting->save()){
                return redirect('administration/settings')->with('message', trans('messages.SETTINGS_UPDATED'));
            }else{
                return redirect('administration/settings')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
            }

        } catch (Exception $e) {
            
        }

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
