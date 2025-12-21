<?php
namespace App\Services;

use App\Models\Module;

class PermissionService
{
    public static function hasPermission($user, $moduleCode, $action): bool
    {
        return $user->roles()
            ->whereHas('permissions', function ($q) use ($moduleCode, $action) {
                $q->where('action', $action)
                  ->whereHas('pivot', function ($p) use ($moduleCode) {
                      $p->whereHas('module', function ($m) use ($moduleCode) {
                          $m->where('code', $moduleCode);
                      });
                  });
            })
            ->exists();
    }
}
