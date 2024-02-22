<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expense_entries';
    /**
     * @name validationRules
     * @desc validation rules
     * @return array
    */
    
    public static function validationRules() {
        return [
            'expense_date' => 'required',
            'expense_type_id' => 'required|exists:expense_types,id',
            'amount' => 'required',
            'user_id' => 'required'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
    */
    public static $validationMessages = [
        'expense_date.required'=> 'Expense Date is required',
        'expense_type_id.required' => 'Expense Type is required',
        'expense_type_id.exists' => 'Selected Expense Type is invalid',
        'amount.required' => 'Amount is required',
        'user_id.required'=> 'User id is required'
    ]; 

     /**
     * @name getExpensesValidationRules
     * @desc validation rules
     * @return array
    */
    public static function getExpensesValidationRules() {
        return [
            'user_id' => 'required'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
    */
    public static $getExpensesValidationMessages = [
        'user_id.required'=> 'User id is required'
    ];

    public static function getExpenses($userId, $perPage, $skip, $filters, $search)
    {
        $query = self::where('user_id', $userId)->with('attachments')
            ->leftJoin('expense_types', 'expense_entries.expense_type_id', '=', 'expense_types.id')
            ->leftJoin('ambulances', 'expense_entries.ambulance_id', '=', 'ambulances.id')
            ->leftJoin('vendors', 'expense_entries.vendor_id', '=', 'vendors.id')
            ->select('expense_entries.*', 'expense_types.name', 'ambulances.ambulance_no', 'vendors.name as vendor');
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('entry_type', 'like', "%$search%")
                    ->orWhere('amount', 'like', "%$search%")
                    ->orWhere('expense_date', 'like', "%$search%")
                    ->orWhere('claim_date', 'like', "%$search%")
                    ->orWhere('expense_types.name', 'like', "%$search%");
            });
        }

        if (!empty($filters['entry_types'])) {
            $query->whereIn('entry_type', $filters['entry_types']);
        }
        
        if (isset($filters['date'][0]) && isset($filters['date'][1])) {
            $startDate = \Carbon\Carbon::createFromFormat('Y/m/d', $filters['date'][0])->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('Y/m/d', $filters['date'][1])->endOfDay();
                
            $query->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('expense_entries.created_at', [$startDate, $endDate]);
            });
        }                  

        if (!empty($filters['status'])) {
            $query->where(function ($query) use ($filters) {
                $query->orWhere('claim_status', $filters['status'])
                        ->orWhere('reimbursement_status', $filters['status']);
            });  
        }

        if (!empty($filters['expense_type_id'])) {
            $query->where('expense_type_id', $filters['expense_type_id']);
        }

        if (!empty($filters['ambulance_id'])) {
            $query->where('ambulance_id', $filters['ambulance_id']);
        }

        if (!empty($filters['type'])) {
            if ($filters['type'] === 'Ambulance') {
                $query->whereNotNull('ambulance_id');
            } elseif ($filters['type'] === 'Other') {
                $query->whereNull('ambulance_id');
            } 
        }   
        $query->skip($skip)->take($perPage);
        return $query->get();
    }

    public static function getAmbulanceExpenses($ambulanceId, $perPage, $skip, $filters, $search)
    {
        $query = self::where('ambulance_id', $ambulanceId)->with('attachments')
            ->leftJoin('expense_types', 'expense_entries.expense_type_id', '=', 'expense_types.id')
            ->leftJoin('ambulances', 'expense_entries.ambulance_id', '=', 'ambulances.id')
            ->leftJoin('vendors', 'expense_entries.vendor_id', '=', 'vendors.id')
            ->leftJoin('users', 'expense_entries.user_id', '=', 'users.id')
            ->select('expense_entries.*', 'expense_types.name' , 'ambulances.ambulance_no', 'vendors.name as vendor', \DB::raw("CONCAT(users.first_name, ' ', users.last_name) AS created_by")
        );

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('entry_type', 'like', "%$search%")
                    ->orWhere('amount', 'like', "%$search%")
                    ->orWhere('expense_date', 'like', "%$search%")
                    ->orWhere('claim_date', 'like', "%$search%")
                    ->orWhere('reimbursement_status', 'like', "%$search%")
                    ->orWhere(\DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'like', "%$search%")
                    ->orWhere('expense_types.name', 'like', "%$search%");
            });
        }

        if (!empty($filters['entry_types'])) {
            $query->whereIn('entry_type', $filters['entry_types']);
        }

        if (isset($filters['date'][0]) && isset($filters['date'][1])) {
            $startDate = \Carbon\Carbon::createFromFormat('Y/m/d', $filters['date'][0])->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('Y/m/d', $filters['date'][1])->endOfDay();
                
            $query->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('expense_entries.created_at', [$startDate, $endDate]);
            });
        }
        
        if (!empty($filters['status'])) {
            $query->where(function ($query) use ($filters) {
                $query->orWhere('claim_status', $filters['status'])
                        ->orWhere('reimbursement_status', $filters['status']);
            });  
        }

        if (!empty($filters['expense_type_id'])) {
            $query->where('expense_type_id', $filters['expense_type_id']);
        }

        $query->skip($skip)->take($perPage);
        return $query->get();
    }

    public function attachments()
    {
        return $this->hasMany(ExpenseAttachment::class);
    }
}
