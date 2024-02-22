<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Auth;

class Organization extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $fillable = [];    
    protected $table = 'organizations';

    protected $auditInclude = ['id', 'item_guid', 'parent_id', 'name', 'owner_name', 'tin', 'industry', 'street_1', 'street_2', 'pincode', 'city', 'district', 'state', 'country', 'phone_code', 'mobile', 'gst', 'status', 'created_at', 'updated_at', 'deleted_at', 'whatsapp_notification', 'authkey', 'currency', 'installation_type', 'organization_type', 'domain', 'staff_limit', 'seller_limit', 'buyer_limit'];
}