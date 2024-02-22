<?php

namespace Modules\Administration\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Administration\Entities\Menu;
use Modules\User\Entities\Role;
use Image;

class MenuController extends Controller
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

    public function index(Request $request,$menu_type = "Side-Menu")
    {

        $user = \Auth::user();

        $organization_type = \Session::get('organization_type');

        if($organization_type == 'MULTIPLE'){
            $getRoles = [\Config::get('constants.ROLES.SELLER'),\Config::get('constants.ROLES.BUYER'),\Config::get('constants.ROLES.SP'),\Config::get('constants.ROLES.OWNER')];
        }else{
            $getRoles = [\Config::get('constants.ROLES.SELLER'),\Config::get('constants.ROLES.BUYER'),\Config::get('constants.ROLES.SP')];
        }

        $roles              =   Role::where('organization_id',$user->organization_id)
                                ->whereIn('name',$getRoles)
                                ->orderby('name','ASC')
                                ->get();

        if(isset($request->role)){
            $role_key = array_search($request->role, array_column($roles->toArray(), 'name'));
            if($role_key == ""){
                $role_key = 0;
            }
        }else{
            $role_key    = 0;
        }
        $role_id = $roles[$role_key]->id;

        $menus =    Menu::where('menu_type',$menu_type)
                    ->where('role_id',$role_id)
                    ->orderby('order','ASC')
                    ->get()->toArray();

        $allMenus = array();

        foreach ($menus as $element) {
            if($element['parent_id'] == 0){
                $allMenus[$element['id']][]=$element;
            }else{
                if(!isset($allMenus[$element['parent_id']]))
                {
                    $allMenus[$element['parent_id']]['children'][]=$element;
                }
                else
                {
                    $allMenus[$element['parent_id']]['children'][]=$element;
                }    
            }

        }

        $allicons = array();
        $iconDir = opendir(public_path('uploads/menu_icons/'));
        while ($file = readdir($iconDir)) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $allicons[] = $file;
        }
        closedir($iconDir);

        // echo '<pre>';
        // print_r($allicons);
        // die;

        return view('administration::menu/index',['roles' => $roles,'menu_type' => $menu_type,'allMenus'=>$allMenus,'allicons'=>$allicons]);
    }

    public function updateOrder(Request $request)
    {

        $user = \Auth::user();
        $i = 1;
        try {
            $menus = $request->menus;

            foreach ($menus as $menu) {
                $element = Menu::findorfail($menu['id']);
                if($element){
                    $element->order = $i;
                    $i++;
                    $element->save();
                }
                if(isset($menu['children'])){
                    foreach ($menu['children'] as $child) {
                        $childElement = Menu::findorfail($child['id']);
                        if($childElement){
                            $childElement->order = $i;
                            $childElement->parent_id = $menu['id'];
                            $i++;
                            $childElement->save();
                        }
                    }
                }
            }
            return array('success'=>true,'item' => array(),'msg'=>trans('messages.MENUS_UPDATED'));
        } catch (Exception $e) {
            return redirect('administration/menu')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        $user = \Auth::user();
        try {
            $rules = array(
                'name' => 'required',      
                'menu_url' => 'required',      
                'menu_target' => 'required',      
                'menu_roles' => 'required'
            );
            $organizationId=$user->organization_id;
            $validator = \Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                $messages = $validator->messages();
                // return redirect('administration/roles')->with('error', $messages);
                return redirect('administration/menu')->withErrors($validator);
            } else {
                /*$icon = "";
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
                    if($extension != 'svg' && $extension != 'jpg' && $extension != 'jpeg' && $extension != 'png'){
                        return redirect('administration/menu')->with('error', 'Invalid image, Image should be a svg,png,jpg or jpeg type');
                    }
                    $image1Name = 'img_1'.$image1Name.'_'.time().'.'.$extension;
                    
                    $destinationPath = public_path('uploads/menu_icons');
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
                    $image1_file = public_path('uploads/menu_icons/'. $image1Name);

                    $icon = $image1Name;
                }*/

                if($request->input("menu_id") && $request->input("menu_id")!='0' && $request->input("menu_id")!=''){
                    $menu = Menu::find($request->input("menu_id"));
                    $existingRole = $menu->role_id; 
                    $existingOrder = $menu->order;
                    $menu->forceDelete();

                    $msg = trans('messages.MENUS_UPDATED');
                }else{
                    $msg = trans('messages.MENUS_ADDED');
                }
                    if($request->exists("menu_roles") && !empty($request->menu_roles)){

                        foreach ($request->menu_roles as $key => $menu_role) {
                            $menu = new Menu();
                            $menu->url = $request->exists("menu_url") ? $request->input("menu_url") : "";
                            $menu->menu_type = $request->exists("menu_type") ? $request->input("menu_type") : "";

                            /*if($icon != ""){
                                $menu->icon = $icon;
                            }*/

                            if(isset($existingOrder)){
                                $menu->order = $existingOrder;
                            }

                            $menu->icon = $request->exists("menu_icon") ? $request->input("menu_icon") : Null;

                            $menu->role_id = $menu_role;
                            $menu->name = $request->exists("name") ? $request->input("name") : "";
                            $menu->description = $request->exists("description") ? $request->input("description") : "";
                            $menu->target = $request->exists("menu_target") ? $request->input("menu_target") : "";
                            $menu->status = $request->exists("status") ? ($request->input("status") == 1) ? 'active' : 'inactive' : "inactive";
                            $menu->save();
                        }
                    }

                
                if($menu){
                    return redirect('administration/menu/'.$request->input("menu_type").'?role='.$request->input("selected_role"))->with('message',$msg);
                    
                }else{
                    return redirect('administration/menu/'.$request->input("menu_type").'?role='.$request->input("selected_role"))->with('error', trans('messages.SOMETHING_WENT_WRONG'));
                }
                
            }
            
        } catch (Exception $e) {
            return redirect('administration/menu')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    public function getRole(Request $request)
    {
        $id = $request->input("id");
        $role   =   Menu::where('id',$id)->first();  
        
        if(!empty($role->toArray())){
            return array('role' => $role,'success'=>true);
        }else{
            return array('success'=>false,'model'=>array());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $id)
    {
        $menu = Menu::findorfail($id);
        $menu_type = $menu->menu_type;
        if($menu->forceDelete()){
            return redirect('administration/menu/'.$menu_type.'?role='.$request->role)->with('message', trans('messages.MENUS_DELETED'));
        }else{
            return redirect('administration/menu/'.$menu_type.'?role='.$request->role)->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    public function jsonFile()
    {
        $arr =  array(
                    'name'=> array(
                            'en' =>'Name', 
                            'hi' =>'Naam', 
                    ),
                    'gender'=> array(
                            'en' =>'Gender', 
                            'hi' =>'Ling', 
                    )
                );

        $file = public_path('results.json');
        $fp = fopen($file, 'w');
        fwrite($fp, json_encode($arr));
        fclose($fp);

        echo json_encode($arr);
    }

    public function updateJson()
    {
        $file = public_path('results.json');
        $jsonString = file_get_contents($file);
        $arr = json_decode($jsonString, true);

        $arr['name']['en'] = 'New Name';
        
        file_put_contents($file, json_encode($arr));
        echo json_encode($arr);

        $tr = ''; 
        foreach ($arr as $key => $a) {
            $tr .= '<tr><td>'.$key.'</td><td>'.$a['en'].'</td><td>'.$a['hi'].'</td></<tr>';
        }

        echo $table = '<table border="1">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>English</th>
                            <th>Hindi</th>
                        </tr>
                    </thead>
                    <tbody>
                    '.$tr.'
                    </tbody>
                </table>';

    } 
}
