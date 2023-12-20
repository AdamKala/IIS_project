<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubComment extends Model
{
    use HasFactory;

    protected $table = 'sub_comments';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'author',
        'text',
        'thread_id',
        'created_by',
        'comment_id'
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}
