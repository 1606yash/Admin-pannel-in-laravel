<?php

namespace Modules\Cms\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiBaseController;
use Config;
use App\Http\Requests\BaseRequest;

class BannerRequest extends BaseRequest
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
            'title' => 'required|max:191',
        ];

        if ($this->isStore()) {
            $rules = array_merge($rules, [
                    'banner' => 'required|mimes:jpg,jpeg,png|max:3000'
                ]
            );
        }

        return $rules;
    }
}
