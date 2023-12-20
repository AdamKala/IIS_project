<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagsThread extends Model
{
    use HasFactory;

    protected $table = 'tags_thread';

    protected $fillable = [
        'tag_id',
        'thread_id'
    ];
}

