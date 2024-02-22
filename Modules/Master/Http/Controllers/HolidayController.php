<?php

namespace Modules\Master\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Holiday as ModelsHoliday;
use Illuminate\Support\Facades\DB;
use App\Exports\FormatExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Imports\HolidaysImport;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $selectedYear = $request->input('year', date('Y'));
        $holidays = ModelsHoliday::select('id','name', 'date')->whereYear('date', $selectedYear)->orderBy('date','asc')->get();

        if ($request->ajax()) {
            // If it's an AJAX request, return a JSON response
            return response()->json(['holidays' => $holidays]);
        }

        return view('master::holiday.index', compact('holidays'));
    }

    public function addHoliday(Request $request)
    {
        try {
            if ($request->hasFile('holiday_doc')) {

               $validate =  Validator::make($request->all(), [
                    'holiday_doc' => 'required|mimes:csv,xls,xlsx',
                ]);

                if ($validate->fails()) {
                    return response()->json(['status' => 'Fail', 'message' => $validate->errors()->first()]);
                }

                $file = $request->file('holiday_doc');
                Excel::import(new HolidaysImport, $file);

                return response()->json(['status' => 'success', 'message' => trans('messages.HOLIDAY_ADDED')]);
            } else {
                $holiday = $request->holiday_name ?? '';
                $holidayDate = $request->holiday_date ? $request->holiday_date : null;
                $holidayDescription = $request->holiday_description ?? '';

                DB::beginTransaction();

                $checkHoliday = ModelsHoliday::whereDate('date', $holidayDate)->first();

                if ($checkHoliday) {
                    return response()->json(['status' => 'Fail', 'message' => trans('messages.HOLIDAY_ALREADY_ADDED')]);
                }

                $data = [];
                $data['date'] = $holidayDate ?? null;
                $data['name'] = $holiday ?? '';
                $data['description'] = $holidayDescription ?? '';
                $data['created_by'] = auth()->id();

                $createHoliday = ModelsHoliday::create($data);

                if ($createHoliday) {
                    DB::commit();
                    return response()->json(['status' => 'success', 'message' => trans('messages.HOLIDAY_ADDED')]);
                }
                DB::rollback();
                return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
            }

            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function deleteBulkHoliday(Request $request)
    {
        try {
            $year = $request->year_id;
            DB::beginTransaction();

            // Fetch holiday data using whereYear
            $holidayData = ModelsHoliday::whereYear('date', $year)->get();

            if ($holidayData->isNotEmpty()) {
                // Iterate through each holiday and delete it
                foreach ($holidayData as $holiday) {
                    $holiday->delete();
                }

                DB::commit();
                return response()->json(['status' => 'success', 'message' => trans('messages.HOLIDAY_DELETED')]);
            }

            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.HOLIDAY_NOT_FOUND')]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function exportFormat(){
        return Excel::download(new FormatExport(), 'bulk_holiday_file_format.xlsx');
    }

    public function getHolidayInfo(Request $request){
        try{
            $id = $request->holiday_id;
            $holidayInfo = ModelsHoliday::where('id',$id)->first();
            return response()->json(['status' => 'success', 'holidayInfo'=>$holidayInfo]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function updateHolidayInfo(Request $request){
        try {
            DB::beginTransaction();

            $id = $request->holiday_edit_id;
            $data =[];
            $holidayInfo = ModelsHoliday::where('id', $id)->first();
            if($holidayInfo){
                $data['name'] = $request->holiday_edit_name ?? null;
                $data['description'] = $request->holiday_edit_description ?? null;
                $updateHolidayInfo = $holidayInfo->update($data);

                if ($updateHolidayInfo) {
                    DB::commit();
                    return response()->json(['status' => 'success', 'message' => trans('messages.HOLIDAY_UPDATED')]);
                }
                DB::rollback();
                return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
            }
            DB::rollback();
            return response()->json(['status' => 'success', 'message' => trans('messages.HOLIDAY_NOT_FOUND')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function deleteHoliday(Request $request){
        try {
            $id = $request->holiday_id;
            DB::beginTransaction();
           
            // Fetch holiday data using whereYear
            $holidayData = ModelsHoliday::where('id', $id)->first();
           
            if ($holidayData) {
                $holidayData->delete();
                DB::commit();
                return response()->json(['status' => 'success', 'message' => trans('messages.HOLIDAY_DELETED')]);
            }

            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.HOLIDAY_NOT_FOUND')]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

}
