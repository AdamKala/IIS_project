<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'privacy',
        'enabled',
        'description',
        'image_path',
        'created_by'
    ];


    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
