<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmbulanceDetail extends Model
{
    use HasFactory;

     protected $table = 'ambulance_details';
    protected $guard_name = 'web';


    protected $fillable = [
        'insurance_available', 'policy_company', 'insurance_start_date', 'insurance_valid_upto', 'puc_available', 'puc_certificate_validity', 'fitness_available', 'fitness_certificate_validity', 'supplier_name', 'bank_id', 'payment_date', 'insurance_upload_path', 'puc_certificates_path', 'fitness_certificate_upload_path', 'ambulance_id', 'policy_number'
    ];
}
