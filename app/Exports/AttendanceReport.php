<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB as FacadesDB;

class AttendanceReport implements FromCollection, WithHeadings, WithMapping
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
        $date = now()->format('Y-m-d');
        $fromDate = $this->request->fromDate ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->request->fromDate)->format('Y-m-d') : $date;
        $toDate = $this->request->toDate ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->request->toDate)->format('Y-m-d') : $date;

        $result = FacadesDB::select('CALL GenerateAttendanceReport(?, ?)', [$fromDate, $toDate]);
        
        // Apply additional conditions to filter the results
        $result = collect($result);

        $result = $result->filter(function ($row) {
            if (!empty($this->request->toArray())) {
                if (isset($this->request->role_id) && !empty($this->request->role_id) && $row->role_id != $this->request->role_id) {
                    return false;
                }
                if (isset($this->request->district_id) && !empty($this->request->district_id) && $row->district_id != $this->request->district_id) {
                    return false;
                }
                if (isset($this->request->user_id) && !empty($this->request->user_id) && $row->user_id != $this->request->user_id) {
                    return false;
                }
                if (isset($this->request->attendance_filter_status) && !empty($this->request->attendance_filter_status) && $row->attendance_status != $this->request->attendance_filter_status) {
                    return false;
                }
            }
            return true;
        });

        // Format the attendance_date in dd/mm/yyyy format
        $result = $result->map(function ($row) {
            $row->attendance_date = \Carbon\Carbon::createFromFormat('Y-m-d', $row->attendance_date)->format('d/m/Y');
            return $row;
        });

        return $result;
    }

    public function map($row): array
    {
        if ($row->attendance_status == 'approved') {
           $status = 'Leave';
        } elseif ($row->attendance_status == 'pending' || $row->attendance_status == 'rejected') {
           $status = 'Absent';
        } else {
           $status= $row->attendance_status;
        }
        return [
            $row->user_name
            ?? '',
            $row->district_name ?? '',
            $row->role_name ?? '',
            $status ?? '',
            $row->attendance_date ?? '',
        ];
    }
    public function headings(): array
    {
        // Define the column headers
        return [
            'User Name',
            'District Name',
            'Role Name',
            'Attendance Status',
            'Attendance Date',
        ];
    }
}
