<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;
    protected $table = 'salary_slips';

    /**
     * @name getSalaryValidationRules
     * @desc validation rules
     * @return array
    */
    public static function getSalaryValidationRules() {
        return [
            'user_id' => 'required'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
    */
    public static $getSalaryValidationMessages = [
        'user_id.required'=> 'User id is required'
    ];
    

    public static function getSalarySlip($userId, $filters)
    {
        $query = self::where('user_id', $userId);
        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }
        if (!empty($filters['month'])) {
            $query->where('month', $filters['month']);
        }    
        return $query->get();
    }
}
