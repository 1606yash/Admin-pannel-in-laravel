<?php

namespace Modules\Administration\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Entities\DefaultSettings;
use Modules\Saas\Entities\Settings;
use Modules\Saas\Entities\HomeSetting;
use Modules\Ecommerce\Entities\Category;
use Auth;
use Image;
class LanguageController extends Controller
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
        $file = public_path('lang/lang.json');
        if(file_exists($file)){
            $jsonString = file_get_contents($file);
            $langData = json_decode($jsonString, true);
            return view('administration::labels/index')->with(compact('langData'));
        }else{
            return view('error/file_not_found');
        }   
    }

    public function store(Request $request){
        $langData = array(
                        'labels' => $request->labels,
                        'validationMessages' => $request->validationMessages,
                        'notificationMessages' => $request->notificationMessages,
                    );

        $defaultFile = public_path('lang/lang.json');
        $defaultFileFp = fopen($defaultFile, 'w');
        fwrite($defaultFileFp, json_encode($langData));
        fclose($defaultFileFp);

        $langEn = $langHi = $langKannada = $langTamil = $langTelugu = array();
        
        $enFile = public_path('lang/en.json');
        $hiFile = public_path('lang/hi.json');
        $kannadaFile = public_path('lang/kannada.json');
        $tamilFile = public_path('lang/tamil.json');
        $teluguFile = public_path('lang/telugu.json');

        foreach ($langData as $type => $data) {
            foreach ($data as $key => $label) {
                $langEn[$type][$key] = $label['en'];
                $langHi[$type][$key] = $label['hi'];
                $langKannada[$type][$key] = $label['kannada'];
                $langTamil[$type][$key] = $label['tamil'];
                $langTelugu[$type][$key] = $label['telugu'];
            }
        }

        if(!empty($langEn)){
            $enFileFp = fopen($enFile, 'w');
            fwrite($enFileFp, json_encode($langEn));
            fclose($enFileFp);
        }

        if(!empty($langHi)){
            $hiFileFp = fopen($hiFile, 'w');
            fwrite($hiFileFp, json_encode($langHi));
            fclose($hiFileFp);
        }

        if(!empty($langKannada)){
            $kannadaFileFp = fopen($kannadaFile, 'w');
            fwrite($kannadaFileFp, json_encode($langKannada));
            fclose($kannadaFileFp);
        }

        if(!empty($langTamil)){
            $tamilFileFp = fopen($tamilFile, 'w');
            fwrite($tamilFileFp, json_encode($langTamil));
            fclose($tamilFileFp);
        }

        if(!empty($langTelugu)){
            $teluguFileFp = fopen($teluguFile, 'w');
            fwrite($teluguFileFp, json_encode($langTelugu));
            fclose($teluguFileFp);
        }


        return redirect('administration/labels')->with('message','Lables updated successfully.');
    }
}
