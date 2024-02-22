<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;
    protected $table = 'holidays';
    protected $guard_name = 'web';
    protected $fillable = ['name', 'date', 'description', 'created_by', 'updated_by'];

    public static function getHolidays($perPage, $skip, $filter)
    {
        $query = self::skip($skip)->take($perPage);    
        if (!empty($filter['year'])) {
            $query->whereYear('date', $filter['year']);
        }
        return $query->get();
    }

    public static function getHolidaysCountForMonth($firstDayOfMonth, $lastDayOfMonth)
    {
        $query = self::whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth]);
        $totalHolidays =  $query->count();
        return $totalHolidays;
    }

    public static function getHolidayForDay($attendanceDate)
    {
        $query = self::whereDate('date', $attendanceDate);
        $totalHolidays =  $query->count();
        return $totalHolidays;
    }
    
}
