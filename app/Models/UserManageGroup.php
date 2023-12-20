<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserManageGroup extends Model
{
    use HasFactory;

    protected $table = 'user_manage_groups';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'user_id',
        'group_id',
        'role_type'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
