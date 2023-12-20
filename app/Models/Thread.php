<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Thread extends Model
{
    use HasFactory;

    protected $table = 'threads';

    protected $fillable = [
        'id',
        'name',
        'text',
        'enabled',
        'slug',
        'group_id',
        'created_by'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tags::class, 'tags_thread', 'thread_id', 'tag_id');
    }
}
