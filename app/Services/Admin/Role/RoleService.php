<?php
namespace App\Services\Admin\Role;
use App\Services\BaseModelService;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleService extends BaseModelService
{
    protected string $modelClass = Role::class;

    public function store()
    {

       $role =  parent::store($this->data); 
        if (isset($this->data['permissions']) && is_array($this->data['permissions'])) {
            $role->syncPermissions($this->data['permissions']);
        } 
        return $role;
    }


    public function update($id){
                    
        if($id == 1){
            throw new \Exception(__('main.can_not_delete', ['text' => "protected role: Admin"]));
        }

        
        $role = parent::update($id , $this->getBasicColumn(['name']));
        if (!empty($this->data['permissions']) && is_array($this->data['permissions'])) {
            $role->syncPermissions($this->data['permissions']);
        }
        return $role;
        
    }


    public function delete($id){
            
        if($id == 1){
            throw new \Exception(__('main.can_not_delete', ['text' => "protected role: Admin"]));
        }
        $role =  DB::table('roles')->where('id', $id)->delete(); 
        return $role;
    }
    

    

    
}