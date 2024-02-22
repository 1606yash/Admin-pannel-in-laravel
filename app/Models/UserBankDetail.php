<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBankDetail extends Model
{
    use HasFactory;
    protected $table = 'user_bank_details';
    protected $guard_name = 'web';
    protected $fillable = ['user_id', 'bank_id', 'account_number', 'ifsc_code', 'bank_proof_image_path', 'created_at', 'updated_at', 'deleted_at'];
}
