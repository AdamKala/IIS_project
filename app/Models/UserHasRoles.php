<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class UserHasRoles extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    protected $table = 'model_has_roles';

    protected $fillable = [
        'id',
        'role_id',
        'model_type',
        'model_id',
        'created_at',
        'updated_at',
        'group_id'
    ];
}
