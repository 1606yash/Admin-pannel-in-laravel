<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmbulanceInventory extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    protected $fillable = ['id', 'ambulance_id', 'name', 'unit_of_measurement', 'capacity', 'quantity', 'status', 'date', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at']; 
     /**
     * @name validationRules
     * @desc validation rules
     * @return array
    */
    public static function validationRules() {
        return [
            'ambulance_id' => 'required|exists:ambulances,id',
            'name' => 'required',
            'unit_of_measurement' => 'required',
            'capacity' => 'required',
            'quantity' => 'required'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
    */
    public static $validationMessages = [
        'ambulance_id.required'=> 'Ambulance id is required',
        'ambulance_id.exists' => 'Selected Ambulance id is invalid.',
        'name.required' => 'Name is required',
        'unit_of_measurement.required' => 'Unit Of Measurement is required',
        'capacity.required' => 'Capacity is required',
        'quantity.required' => 'Quantity is required',
    ]; 

    /**
     * @name validationRules
     * @desc validation rules
     * @return array
    */
    public static function validationUpdateRules() {
        return [
            'id' => 'required|exists:ambulance_inventories,id',
            'status' => 'required',
            'date' => 'required',
            'quantity' => 'required',
            'updated_by' => 'required'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
    */
    public static $validationUpdateMessages = [
        'id.required'=> 'Id is required',
        'id.exists' => 'Selected id is invalid.',
        'status.required' => 'Status is required',
        'date.required' => 'Date is required',
        'quantity.required' => 'Quantity is required',
        'updated_by.required' => 'Updated by is required'
    ]; 

    public static function getAmbulanceInventoriesByAmbulanceId($ambulanceId, $perPage, $skip)
    {
        $query = self::where('ambulance_id', $ambulanceId);
        $query->skip($skip)->take($perPage);
        return $query->get();    
    }

    public static function getAmbulanceInventoriesById($id, $perPage, $skip)
    {
        $query = InventoryManagement::select(
            'inventory_managements.*',
            \DB::raw("CONCAT(users.first_name, ' ', users.last_name) AS changed_by")
        )->leftJoin('users', 'users.id', '=', 'inventory_managements.created_by')->where('inventory_id', $id);
        $query->skip($skip)->take($perPage);
        return $query->get();
    }
}
