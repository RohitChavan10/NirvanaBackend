<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
      protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_roles',
            'role_id',
            'user_id'
        );
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_module_permissions',
            'role_id',
            'permission_id'
        )->withPivot('module_id');
    }

    public function modules()
{
    // Many-to-many through role_module_permissions table
    return $this->belongsToMany(
        Module::class,
        'role_module_permissions',
        'role_id',
        'module_id'
    )->withPivot('permission_id');
}
}
