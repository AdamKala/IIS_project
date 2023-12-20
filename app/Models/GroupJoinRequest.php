<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class GroupJoinRequest extends Model
{
    use HasFactory;

    protected $table = 'group_join_requests';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'group_id',
        'user_id',
        'moderator_id',
        'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function acceptJoin()
    {
        $this['moderator_id'] = Auth::id();
        $this['status'] = 'prijat';
        $this->update();
    }

    public function rejectJoin()
    {
        $this['moderator_id'] = Auth::id();
        $this['status'] = 'odmitnut';
        $this->update();
    }
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
