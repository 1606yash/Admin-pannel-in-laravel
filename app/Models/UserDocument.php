<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class UserDocument extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'user_documents';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'user_id', 'doc_type', 'doc_url', 'doc_number',
        'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by'
    ];

    public static function addUserDocument($data)
    {
        return self::insert($data);
    }
    public static function updateUserDocument($userId, $data)
    {
        $userDocument = self::where('user_id', $userId)->where('doc_type', $data['doc_type'])->first();
        if ($userDocument) {
            return $userDocument->update($data);
        } else {
            return self::create($data);
        }
    }

    public static function getUserDocument($userId)
    {
        return self::where('user_id', $userId)
            ->select(['doc_url', 'doc_type', 'user_id', 'id as user_document_id'])->get();
    }
    public static function getUserDocumentByDocType($userId, $docType)
    {
        return self::where('user_id', $userId ?? null)
            ->where('doc_type', $docType ?? null)
            ->first();
    }
}
