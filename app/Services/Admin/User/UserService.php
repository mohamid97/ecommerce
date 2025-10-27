<?php
namespace App\Services\Admin\User;

use App\Models\User;
use App\Services\BaseModelService;
use Spatie\Permission\Models\Role;

class UserService extends BaseModelService
{
    protected string $modelClass = User::class;

    public function store()
    {
        $this->hassBassword();
        $user = parent::store($this->data);
        $this->assingRoleUser($user);
        return $user;
    }

    private function hassBassword()
    {
        $this->data['password'] = bcrypt($this->data['password']);
        $this->data['type'] = 'admin';
    }
    private function assingRoleUser($user)
    {
        $role = Role::where(['name' => $this->data['role']])->first();
        if (isset($role) && $role != null) {    
            $user->assignRole($role);
        }

    }


    
    

    
}