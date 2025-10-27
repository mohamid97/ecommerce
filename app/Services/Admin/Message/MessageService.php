<?php
namespace App\Services\Admin\Message;

use App\Models\Api\Front\Message as MessageModel;
use App\Services\BaseModelService;

class MessageService extends BaseModelService
{

    protected string $modelClass = MessageModel::class;


    public function all($request){
        $message = parent::all($request);
        return $message;
    }

    public function applySearch($query, $search)
    {
        return $query->where('name', 'like', "%$search%")
                     ->orWhere('email', 'like', "%$search%")
                     ->orWhere('subject', 'like', "%$search%");
                
    }

}