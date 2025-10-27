<?php
namespace App\Services\Admin\Permission;

use App\Services\BaseModelService;
use Spatie\Permission\Models\Permission;

class PermissionService extends BaseModelService
{
    protected string $modelClass = Permission::class;

    public function all($request){
        $permissions = parent::all($request);
        return $permissions;
        return $this->groupPermissionsByModel($permissions);
    }


    private function groupPermissionsByModel($permissions)
    {
        $groupedPermissions = [];
        
        foreach ($permissions as $permission) {
            // Split permission name into parts
            $parts = explode(' ', $permission->name);
            
            if (count($parts) >= 2) {
                $action = $parts[0];
                $model = implode(' ', array_slice($parts, 1));
                
                if (!isset($groupedPermissions[$model])) {
                    $groupedPermissions[$model] = [];
                }
                
                $groupedPermissions[$model][] = $permission->name;
            }
        }

        return $groupedPermissions;
    }

    
}