<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupUser extends Model
{
    use HasFactory;

    protected $table = 'group_user';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'group_id',
        'user_id',
        'enabled'
    ];

    public function group(): BelongsTo{
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class, 'user_id');
    }
}
