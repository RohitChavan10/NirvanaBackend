<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleModulePermission extends Model
{
    protected $table = 'role_module_permissions';
    protected $fillable = [
        'role_id',
        'module_id',
        'permission_id'
    ];
}
