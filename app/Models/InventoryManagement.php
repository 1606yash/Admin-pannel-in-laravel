<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryManagement extends Model
{
    use HasFactory;
    protected $table = 'inventory_managements';
    protected $guard_name = 'web';
    protected $fillable = ['inventory_id', 'type', 'quantity', 'created_by', 'created_at', 'updated_at', 'deleted_at', 'unit_of_measurement', 'date'];

}
