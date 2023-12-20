<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'comment_id',
        'vote_type',
        'created_at',
        'updated_at'
    ];
}
