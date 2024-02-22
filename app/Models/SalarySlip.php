<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarySlip extends Model
{
    use HasFactory;
    protected $table = 'salary_slips';
    protected $guard_name = 'web';
    protected $fillable = ['user_id', 'salary_date', 'month', 'year', 'basic_salary', 'house_rent_allowance', 'conveyance_allowance', 'special_allowances', 'professional_tax', 'gross_salary', 'net_payable_amount', 'pf_contribution', 'created_at', 'updated_at', 'deleted_at'];
}
