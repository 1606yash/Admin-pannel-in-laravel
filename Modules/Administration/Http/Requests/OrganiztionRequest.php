<?php

namespace Modules\Administration\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiBaseController;
use Config;
use App\Http\Requests\BaseRequest;

class OrganiztionRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = array();

        $rules = [
            'organizationName' => 'required|max:191',
            
            'ownerName' => 'required|max:191',
            'address1' => 'required',
            'country' => 'required',
            'state' => 'required',
            'district' => 'required',
            'city' => 'required',
            'pincode' => 'required|numeric',
        ];

        if($this->request->get('organizationId')) {
            $organizationId = $this->request->get('organizationId');
            $rules = array_merge($rules, [
                'organizationTin' => 'required|unique:organizations,tin,' . $organizationId,
            ]);   
        }else{
            $rules = array_merge($rules, [
                'organizationTin' => 'required|unique:organizations,tin',
            ]);

        }

        return $rules;
    }
}
