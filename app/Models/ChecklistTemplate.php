<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistTemplate extends Model
{
    protected $fillable = [
        'task_name', 'is_required', 'sort_order', 'is_active',
        // NOVÉ:
        'target_type',           // all, type, user
        'target_employee_type',  // kitchen, floor...
        'target_user_id',        // ID uživatele
    ];

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
