<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['code', 'name'];

     /**
     * Permissions associated with this module
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_module_permissions',
            'module_id',
            'permission_id'
        )->withPivot('role_id');
    }
}
