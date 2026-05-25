<?php
namespace App\Services\Admin\User;

use App\Models\Api\Ecommerce\Order;
use App\Models\User;
use App\Services\BaseModelService;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder;

class UserService extends BaseModelService
{
    protected string $modelClass = User::class;


    public function all($request)
    {
        $query = $this->modelClass::query();
        if (!empty($request['search']) && method_exists($this, 'applySearch')) {
         
            $query = $this->applySearch($query, $request['search']);
        }

        if (!empty($request['orderBy']) && method_exists($this, 'orderBy')) {
            $query = $this->orderBy($query, $request['orderBy'] , $request['orderDirection'] ?? 'DESC');
        }
        $query = $this->type($query, 'admin');
        $users = $query->paginate(15);
        return $users;
        
    }


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


    public function applySearch(Builder $query, string $search)
    {
        return $query->whereAny([
            'username',
            'email',
            'phone',
            'first_name',
            'last_name',
        ], 'like', "%$search%");
    }

    public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    {
        return $query->orderBy($orderBy, $direction);
    }

    public function type(Builder $query, string $type)
    {
        return $query->where('type', $type);
    }






    // order sumary for user
    public function orderSummary(int $userId): array
    {
        $user = User::with('profile.government')->findOrFail($userId);
        $ordersQuery = Order::where('user_id', $user->id);

        return [
            'user' => $user,
            'total_orders' => (clone $ordersQuery)->count(),
            'total_spent' => (float) (clone $ordersQuery)
                ->where('payment_status', 'paid')
                ->sum('total'),
            'latest_orders' => (clone $ordersQuery)
                ->with(['user'])
                ->withCount('items')
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }



    
}
