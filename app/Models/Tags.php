<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tags extends Model
{
    use HasFactory;

    protected $table = 'tags';

    protected $fillable = [
        'id',
        'name',
        'thread_id',
    ];

    public function threads(): BelongsToMany
    {
        return $this->belongsToMany(Thread::class, 'tags_thread', 'tag_id', 'thread_id');
    }
}
