<?php

namespace Modules\Master\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\State;
use App\Models\District;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;


class MasterController extends Controller
{

    public function stateList(Request $request)
    {
        $data = State::select('states.id', 'states.state_name', DB::raw('CONCAT(u1.first_name, " ", u1.last_name) as created_by'), DB::raw('CONCAT(u2.first_name, " ", u2.last_name) as updated_by'))
            ->leftJoin('users as u1', 'states.created_by', '=', 'u1.id')
            ->leftJoin('users as u2', 'states.updated_by', '=', 'u2.id')
            ->orderBy('states.id', 'ASC')
            ->where(function ($query) use ($request) {
                if (!empty($request->toArray())) {
                    if (isset($request->stateName) && (!empty($request->stateName))) {
                        $query->where('state_name', 'LIKE', '%' . $request->stateName . '%');
                    }
                }
            })
            ->get();

        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    if (true) {

                        $btn .= "<li>
                                        <a href='#' data-target='addState' data-id='" . $row->id . "' class='editItem toggle'>
                                            <em class='icon ni ni-edit'></em> <span>Edit</span>
                                        </a>
                                    </li>";
                    }
                    $confirmMsg = 'Are you sure, you want to delete it?';
                    if (true) {
                        $btn .= "<li>
                                        <a href='#' data-id='" . $row->id . "' class='eg-swal-av3'>
                                            <em class='icon ni ni-trash'></em> <span>Delete</span>
                                        </a>
                                    </li>";
                    }

                    $btn .= "</ul></div></div></li></ul>";

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('master::stateList', ['states' => $data]);
    }

    public function storeState(Request $request)
    {
        try {

            $rules = array(
                'state_name' => 'required',
            );
            $user = \Auth::user();
            // echo "<pre>";
            // print_r($user->id);
            // die;
            $validator = \Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                $messages = $validator->messages();
                // return redirect('ecommerce/brands')->with('validator_error', $messages);
                //return redirect('master/state')->withErrors($validator);
                return response()->json(['status' => 'Fail', 'message' => $validator->errors()->first()]);
            } else {

                if ($request->input("id") && $request->input("id") != '0' && $request->input("id") != '') {
                    $checkStateName = State::where('state_name', $request->input("state_name"))
                        ->where('id', !$request->input("id"))->first();
                    if ($checkStateName) {
                        //return redirect('master/state')->with('error', trans('messages.STATE_ALREADY_EXISTS'));
                        return response()->json(['status' => 'Fail', 'message' => trans('messages.STATE_ALREADY_EXISTS')]);
                    }
                    $state = State::find($request->input("id"));
                    $state->state_name = $request->input("state_name");
                    $state->updated_by = $user->id ?? null;
                    $msg = trans('messages.STATE_UPDATED');
                } else {
                    $checkStateName = State::where('state_name', $request->input("state_name"))->first();
                    if ($checkStateName) {
                        //return redirect('master/state')->with('error', trans('messages.STATE_ALREADY_EXISTS'));
                        return response()->json(['status' => 'Fail', 'message' => trans('messages.STATE_ALREADY_EXISTS')]);
                    }
                    $state = new State();
                    $state->state_name = $request->input("state_name");
                    $state->created_by = $user->id ?? null;
                    $state->updated_by = $user->id ?? null;
                    $msg = trans('messages.STATE_ADDED');
                }

                if ($state->save()) {
                    //return redirect('master/state')->with('message', $msg);
                    return response()->json(['status' => 'success', 'message' => $msg]);
                } else {
                    //return redirect('master/state')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
                    return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
                }
            }
        } catch (Exception $e) {
            //return redirect('master/state')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    public function destroyState(Request $request)
    {
        $id = $request->input("id");
        $item = State::findOrfail($id);
        if ($item->delete()) {
            return array('states' => array(), 'success' => true, 'msg' => 'success');
        } else {
            return array('success' => false, 'states' => array(), 'msg' => 'fails');
        }
    }

    public function getState(Request $request)
    {
        $id = $request->input("id");
        $state = State::where('id', $id)->first();

        if (!empty($state->toArray())) {
            return array('state' => $state, 'success' => true);
        } else {
            return array('success' => false, 'state' => array());
        }
    }

    public function districtList(Request $request)
    {

        $data = District::select('districts.id', 'districts.district_name', DB::raw('CONCAT(u1.first_name, " ", u1.last_name) as created_by'), DB::raw('CONCAT(u2.first_name, " ", u2.last_name) as updated_by'), 's1.state_name as state_name')
            ->leftJoin('users as u1', 'districts.created_by', '=', 'u1.id')
            ->leftJoin('users as u2', 'districts.updated_by', '=', 'u2.id')
            ->leftJoin('states as s1', 'districts.state_id', '=', 's1.id')
            ->where(function ($query) use ($request) {
                if (!empty($request->toArray())) {
                    if (isset($request->filter_state_id) && (!empty($request->filter_state_id))) {
                        $query->where('districts.state_id', $request->filter_state_id);
                    }
                    if (isset($request->creation_date) && !empty($request->creation_date)) {
                        $this->filterByCreationDate($query, $request->creation_date);
                    }
                }
            })
            ->get();

        if ($request->ajax()) {
            /*echo '<pre>';
            print_r($data->toArray());die;*/
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    if (true) {

                        $btn .= "<li>
                                        <a href='#' data-target='addDistrict' data-id='" . $row->id . "' class='editItem toggle'>
                                            <em class='icon ni ni-edit'></em> <span>Edit</span>
                                        </a>
                                    </li>";
                    }
                    $confirmMsg = 'Are you sure, you want to delete it?';
                    if (true) {
                        $btn .= "<li>
                                        <a href='#' data-id='" . $row->id . "' class='eg-swal-av3'>
                                            <em class='icon ni ni-trash'></em> <span>Delete</span>
                                        </a>
                                    </li>";
                    }

                    $btn .= "</ul></div></div></li></ul>";

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $states = State::get();

        return view('master::districtList', ['districts' => $data, 'states' => $states]);
    }

    public function storeDistrict(Request $request)
    {
        try {
            $rules = array(
                'district_name' => 'required',
                'state_id' => 'required',
            );
            $user = \Auth::user();
            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->messages();
                // return redirect('ecommerce/brands')->with('validator_error', $messages);
                //return redirect('master/district')->withErrors($validator);
                return response()->json(['status' => 'Fail', 'message' => $validator->errors()->first()]);
            } else {
                if ($request->input("id") && $request->input("id") != '0' && $request->input("id") != '') {
                    //check district name is unique
                    $checkDistrictName = District::where('district_name', $request->input("district_name"))
                        ->where('id', !$request->input("id"))->first();
                    if ($checkDistrictName) {
                        // return redirect('master/district')->with('error', trans('messages.DISTRICT_ALREADY_EXIST'));
                        return response()->json(['status' => 'Fail', 'message' => trans('messages.DISTRICT_ALREADY_EXISTS')]);
                    }
                    $district = District::find($request->input("id"));
                    $district->state_id = $request->input("state_id");
                    $district->district_name = $request->input("district_name");
                    $district->updated_by = $user->id ?? null;
                    $msg = trans('messages.DISTRICT_UPDATED');
                } else {
                    $checkDistrictName = District::where('district_name', $request->input("district_name"))->first();
                    if ($checkDistrictName) {
                        //return redirect('master/district')->with('error', trans('messages.DISTRICT_ALREADY_EXIST'));
                        return response()->json(['status' => 'Fail', 'message' => trans('messages.DISTRICT_ALREADY_EXISTS')]);
                    }
                    $district = new District();
                    $district->district_name = $request->input("district_name");
                    $district->state_id = $request->input("state_id");
                    $district->created_by = $user->id ?? null;
                    $district->updated_by = $user->id ?? null;
                    $msg = trans('messages.DISTRICT_ADDED');
                }

                if ($district->save()) {
                    //return redirect('master/district')->with('message', $msg);
                    return response()->json(['status' => 'success', 'message' => $msg]);
                } else {
                    //return redirect('master/district')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
                    return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
                }
            }
        } catch (Exception $e) {
            //return redirect('master/district')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    public function destroyDistrict(Request $request)
    {
        $id = $request->input("id");
        $item = District::findOrfail($id);
        if ($item->delete()) {
            return array('districts' => array(), 'success' => true, 'msg' => 'success');
        } else {
            return array('success' => false, 'district' => array(), 'msg' => 'fails');
        }
    }

    public function getDistrict(Request $request)
    {
        $id = $request->input("id");
        $state = District::where('id', $id)->first();

        if (!empty($state->toArray())) {
            return array('district' => $state, 'success' => true);
        } else {
            return array('success' => false, 'district' => array());
        }
    }

    protected function filterByCreationDate($query, $creationDate)
    {
        // Check the provided creationDate and apply the corresponding filter to the query
        if ($creationDate == 'LastThreeMonth') {
            $query->where('districts.created_at', '>=', Carbon::now()->subMonths(3));
        } elseif ($creationDate == 'LastSixMonth') {
            $query->where('districts.created_at', '>=', Carbon::now()->subMonths(6));
        } elseif ($creationDate == 'CurrentYear') {
            $query->whereYear('districts.created_at', Carbon::now()->year);
        } elseif ($creationDate == 'LastYear') {
            $query->whereYear('districts.created_at', Carbon::now()->subYear()->year);
        } elseif ($creationDate == 'LastThreeYear') {
            $query->where('districts.created_at', '<', Carbon::now()->subYears(3));
        }
    }
}
