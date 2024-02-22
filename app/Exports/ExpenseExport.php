<?php

namespace App\Exports;

use App\Models\Expense as ModelsExpense;
use Illuminate\Http\Request; // Add this line to import the Request class
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpenseExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ModelsExpense::select(
            'ambulances.ambulance_no',
            'ambulances.chassis_no',
            'expense_entries.entry_type',
            'expense_types.name as expense_type_name',
            'expense_entries.amount',
            \DB::raw('DATE_FORMAT(expense_entries.expense_date, "%d/%m/%Y") as expense_date'),
            'expense_entries.claim_status',
            'expense_entries.reimbursement_status',
        )
            ->leftJoin('ambulances', 'expense_entries.ambulance_id', '=', 'ambulances.id')
            ->leftJoin('expense_types', 'expense_entries.expense_type_id', '=', 'expense_types.id')
            ->where(function ($query) {
                // Change $request to $this->request
                if (!empty($this->request->toArray())) {
                    if (isset($this->request->entry_type) && !empty($this->request->entry_type)) {
                        $query->where('expense_entries.entry_type', 'LIKE', '%' . $this->request->entry_type . '%');
                    }
                    if (isset($this->request->vehicle_id) && !empty($this->request->vehicle_id)) {
                        $query->where('expense_entries.ambulance_id', $this->request->vehicle_id);
                    }
                    if (isset($this->request->reimbursement_status) && !empty($this->request->reimbursement_status)) {
                        $query->where('expense_entries.reimbursement_status', 'LIKE', '%' . $this->request->reimbursement_status . '%');
                    }
                    if (isset($this->request->status) && !empty($this->request->status)) {
                        $query->where('status', $this->request->status);
                    }
                    if (isset($this->request->expense_type_id) && !empty($this->request->expense_type_id)) {
                        $query->where('expense_entries.expense_type_id', $this->request->expense_type_id);
                    }
                    if (!empty($this->request->fromDate) && !empty($this->request->toDate)) {
                        $query->whereRaw("expense_entries.expense_date between '" . \Carbon\Carbon::createFromFormat('m/d/Y', $this->request->fromDate)->format('Y-m-d') . "' AND '" . \Carbon\Carbon::createFromFormat('m/d/Y', $this->request->toDate)->format('Y-m-d') . "'");
                    }
                    //  Add more conditions as needed
                }
            })
            ->get();
    }

    public function headings(): array
    {
        // Define the column headers
        return [
            'Ambulance No',
            'Chassis No',
            'Entry Type',
            'Expense Type Name',
            'Amount',
            'Expense Date',
            'Claim Status',
            'Reimbursement Status',
        ];
    }
}
