<?php

namespace App\Traits;

use Spatie\Permission\Models\Role;

trait HasRoleTypes
{
    public function hasRoleType($roleType)
    {
        // Check if the user has any role with the specified role_type
        return $this->roles()->where('role_type', $roleType)->exists();
    }

    public function getRoleTypes()
    {
        // Return all unique role types associated with the user
        return $this->roles->pluck('role_type')->unique();
    }
}
