<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Task extends Model
{
    use HasFactory;
    protected $table = 'tasks';
    protected $enumStatus = ['Created', 'Assigned', 'In Progress', 'Cancelled', 'Completed'];
    /**
     * @name validationRules
     * @desc validation rules
     * @return array
    */
    public static function validationRules() {
        return [
            'title' => 'required',
            'description' => 'required',
            'priority' => 'required',
            'assigned_to' => 'required',
            'user_id' => 'required'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
    */
    public static $validationMessages = [
        'title.required'=> 'Title id is required',
        'description.required' => 'Description is required',
        'priority.required' => 'Priority is required',
        'assigned_to.required' => 'Assigned To is required',
        'user_id.required'=> 'User id is required'
    ]; 

    /**
     * @name getTasksValidationRules
     * @desc validation rules
     * @return array
    */
    public static function getTasksValidationRules() {
        return [
            'user_id' => 'required'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
    */
    public static $getTasksValidationMessages = [
        'user_id.required'=> 'User id is required'
    ];

    public static function getTasks($userId, $perPage, $skip)
    {
        $query = self::select('tasks.*', \DB::raw("CONCAT(users.first_name, ' ', users.last_name) AS assignee_name"), 'r.role_name', \DB::raw("DATE_FORMAT(tasks.created_at, '%d/%m/%Y') AS creation_date"), \DB::raw("DATE_FORMAT(tasks.updated_at, '%d/%m/%Y') AS updated_date"))
            ->leftJoin('users', 'tasks.assigned_to', '=', 'users.id')
            ->leftJoin('roles as r', 'r.id', '=', 'users.role_id')
            ->where('tasks.created_by', $userId)
            ->orWhere('tasks.assigned_to', $userId);
        $query->skip($skip)->take($perPage);
        return $query->get();
    }
}
