<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseAttachment extends Model
{
    use HasFactory;
    protected $table = 'expense_attachments';
    protected $fillable = ['expense_id', 'attachment_path'];
}
