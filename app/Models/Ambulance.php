<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Ambulance extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'ambulances';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'ambulance_no', 'district_id', 'state_id', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'registration_number_available', 'make', 'inauguration_date', 'status', 'number_plate_available', 'chassis_no', 'registration_certificate_available', 'purchase_paper_available', 'fastags_available', 'sponsor_name', 'invoice_no', 'invoice_date', 'date_of_delivery', 'registration_date', 'entry_date', 'additional_notes', 'number_plate_image_path', 'registration_certificate_path', 'purchase_paper_path', 'fastags_image_path'
    ];

    public static function getAmbulancesByDistrictID($districtId, $perPage, $skip, $filters, $search)
    {
        $query = self::where('district_id', $districtId)->leftJoin('ambulance_details', 'ambulances.id', '=', 'ambulance_details.ambulance_id')->leftJoin('districts', 'districts.id', '=', 'ambulances.district_id')->select(
            'ambulances.id',
            'ambulances.ambulance_no',
            'ambulances.status',
            'districts.district_name',
            'ambulances.station_location',
            'ambulances.service_location',
            'ambulances.chassis_no',
            'ambulances.inauguration_date',
            'ambulances.make',
            'ambulances.number_plate_available',
            'ambulances.invoice_date',
            'ambulances.invoice_no',
            'ambulances.registration_date',
            'ambulance_details.insurance_start_date',
            'ambulance_details.insurance_valid_upto',
            'ambulance_details.policy_company',
            'ambulance_details.policy_number',
            'ambulance_details.puc_available',
            'ambulance_details.puc_certificate_validity',
            'ambulance_details.fitness_available',
            'ambulance_details.fitness_certificate_validity',
            'ambulances.additional_notes',
            'ambulances.registration_certificate_path',
            'ambulance_details.puc_certificates_path',
            'ambulance_details.insurance_upload_path',
            'ambulances.purchase_paper_path',
            'ambulance_details.fitness_certificate_upload_path',
        );
        if ($search) {
            $query->where('ambulance_no', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%")
                ->orWhere('station_location', 'like', "%$search%")
                ->orWhere('service_location', 'like', "%$search%");
        }

        if (!empty($filters['status'])) {
            $query->whereIn('status', $filters['status']);
        }

        if (!empty($filters['station_location'])) {
            $query->where('station_location', $filters['station_location']);
        }

        if (!empty($filters['service_location'])) {
            $query->where('service_location', $filters['service_location']);
        }
        $query->skip($skip)->take($perPage);
        return $query->get();;
    }
    public static function getAllAmbulances()
    {
        return self::all();
    }

    public static function ambulanceDetails($ambulanceId)
    {
        $details = self::leftJoin('ambulance_details', 'ambulances.id', '=', 'ambulance_details.ambulance_id')
            ->leftJoin('districts', 'districts.id', '=', 'ambulances.district_id')
            ->leftJoin('bank_lists', 'bank_lists.id', '=', 'ambulance_details.bank_id')
            ->leftJoin('users', 'users.id', '=', 'ambulances.created_by')
            ->where('ambulances.id', $ambulanceId)
            ->select(
                'ambulances.id',
                'ambulances.ambulance_no',
                'ambulances.status',
                'ambulances.district_id',
                'districts.district_name',
                'ambulances.station_location',
                'ambulances.service_location',
                'ambulances.chassis_no',
                'ambulances.inauguration_date',
                'ambulances.make',
                'bank_lists.bank_name',
                'ambulance_details.supplier_name',
                'ambulance_details.payment_date',
                'ambulances.number_plate_available',
                'ambulances.number_plate_image_path',
                'ambulances.registration_certificate_available',
                'ambulances.purchase_paper_available',
                'ambulances.fastags_available',
                'ambulances.invoice_date',
                'ambulances.invoice_no',
                'ambulances.registration_date',
                'ambulance_details.insurance_available',
                'ambulance_details.insurance_start_date',
                'ambulance_details.insurance_valid_upto',
                'ambulance_details.policy_company',
                'ambulance_details.policy_number',
                'ambulance_details.puc_available',
                'ambulance_details.puc_certificate_validity',
                'ambulance_details.fitness_available',
                'ambulance_details.fitness_certificate_validity',
                'ambulances.additional_notes',
                'ambulances.registration_certificate_path',
                'ambulance_details.puc_certificates_path',
                'ambulance_details.insurance_upload_path',
                'ambulances.fastags_image_path',
                'ambulances.purchase_paper_path',
                'ambulance_details.fitness_certificate_upload_path',
                'ambulances.sponsor_name',
                'ambulances.entry_date',
                'ambulances.date_of_delivery',
                \DB::raw("CONCAT(users.first_name, ' ', users.last_name) AS ambulance_created_by")
            )
            ->first();
        return $details;
    }

    public static function getAllChassisNumber()
    {
        $chassisNo = self::select('id', 'chassis_no')->get();
        if ($chassisNo) {
            return $chassisNo;
        }
        return false;
    }
}
