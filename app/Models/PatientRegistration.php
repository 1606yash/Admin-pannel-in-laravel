<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class PatientRegistration extends Model
{
    use HasFactory;

    protected $table = 'patient_registrations';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'user_id', 'ambulance_id', 'service_area_id', 'requester_name', 'pickup_address', 'pickup_latitude', 'mobile_number', 'relation', 'patient_name', 'age', 'gender','reason','drop_address','drop_latitude',
        'start_address', 'start_latitude', 'start_meter_reading', '	pickup_meter_reading', 'drop_meter_reading', 'distance_covered', 'service_duration', 'reject_reason', 'patient_status', 'cancel_reason', 'district_id', 'case_status', 'request_status', 'pickup_longitude', 'drop_longitude', 'start_longitude', 'created_by', 'updated_by','created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * @name validationRules
     * @desc validation rules
     * @return array
     */
    public static function validationRules()
    {
        return [
            'request_id' => 'required',
            'requester_name' => 'required',
            'mobile_number' => 'required',
            'patient_name' => 'required',
            'age' => 'required',
            'gender' => 'required',
            'reason' => 'required',
            'pickup_address' => 'required',
            'drop_address' => 'required'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
     */
    public static $validationMessages = [
        'request_id.required' => 'Request id is required',
        'requester_name.required' => 'Requester name is required',
        'mobile_number.required' => 'Mobile number is required',
        'age.required' => 'Age is required',
        'gender.required' => 'Gender is required',
        'reason.required' => 'Reason is required',
        'pickup_address.required' => 'Pickup address is required',
        'drop_address.required' => 'Drop address is required',
    ];

    public static function getCasesByUserId($userId, $perPage, $skip, $filters, $search)
    {
        $query = self::where('user_id', $userId);

        $acceptedStatus = Config::get('constants.REQUEST_STATUS.Accepted');
        $rejectedStatus = Config::get('constants.REQUEST_STATUS.Rejected');
        $cancelledStatus = Config::get('constants.REQUEST_STATUS.Cancelled');

        if ($search) {
            $query->where(function ($query) use ($search, $filters, $acceptedStatus) {
                $query->where('request_id', 'like', "%$search%")
                    ->orWhere('requester_name', 'like', "%$search%");

                if ($filters['type'] === $acceptedStatus) {
                    $query->orWhere('pickup_address', 'like', "%$search%")
                        ->orWhere('drop_address', 'like', "%$search%");
                } elseif ($filters['type'] === 'rejected_and_cancelled') {
                    $query->orWhere('pickup_address', 'like', "%$search%")
                        ->orWhere('rejected_reason', 'like', "%$search%"); // Search in rejected_reason for rejected/cancelled types
                }
            });
        }

        if (!empty($filters['type'])) {
            if ($filters['type'] === $acceptedStatus) {
                $query->where('request_status', $acceptedStatus);
            } elseif ($filters['type'] === 'rejected_and_cancelled') {
                $query->where(function ($query) use ($rejectedStatus, $cancelledStatus) {
                    $query->where('request_status', $rejectedStatus)
                        ->orWhere('request_status', $cancelledStatus);
                });
            }
        }


        if (isset($filters['date'][0]) && isset($filters['date'][1])) {
            $startDate = \Carbon\Carbon::createFromFormat('Y/m/d', $filters['date'][0])->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('Y/m/d', $filters['date'][1])->endOfDay();

            $query->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });
        }

        if (!empty($filters['creation_date_range'])) {
            switch ($filters['creation_date_range']) {
                case 'last_week':
                    $query->where('created_at', '>=', now()->subWeek()->startOfWeek())
                        ->where('created_at', '<=', now()->subWeek()->endOfWeek());
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', now()->subMonth()->month);
                    break;
                default:
                    break;
            }
        }

        $query->skip($skip)->take($perPage);
        return $query->get();
    }

    public static function getCurrentCaseRequest($userId)
    {
        $query = self::where('user_id', $userId)
            ->whereNull('case_status')
            ->orWhere('case_status', '!=', Config::get('constants.CASE_STATUS.Drop'))
            ->latest('created_at');
        return $query->first();
    }

    public static function getTotalCasesCount($userId)
    {
        $query = self::where('user_id', $userId);
        return $query->count();
    }

    public static function getTodayCasesCount($userId)
    {
        $query = self::where('user_id', $userId)->whereDate('created_at', \Carbon\Carbon::today());
        return $query->count();
    }

    public static function getThisWeekCasesCount($userId)
    {
        $startOfWeek = \Carbon\Carbon::now()->startOfWeek();
        $endOfWeek = \Carbon\Carbon::now()->endOfWeek();
        $query = self::where('user_id', $userId)->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
        return $query->count();
    }

    public static function getThisMonthCasesCount($userId)
    {
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
        $endOfMonth = \Carbon\Carbon::now()->endOfMonth();
        $query = self::where('user_id', $userId)->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        return $query->count();
    }
}
