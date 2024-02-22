<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    use HasFactory;
    protected $table = 'expense_types';
    protected $guard_name = 'web';

    public static function getAllExpenseType(){
        return self::all();
    }
}
