<?php
namespace App\Service\Admin\Applicant;

use App\Models\Api\Admin\Applicant;
use App\Services\BaseModelService;
use App\Traits\HandlesUpload;
use Illuminate\Database\Eloquent\Builder;

class ApplicantService extends BaseModelService{
    use HandlesUpload;
    protected string $model = Applicant::class;


    public function all($request){
        $applicant = parent::all($request);
        return $applicant;
    }
    public function store()
    {
        $this->data['cv'] = $this->uploadFile($this->data['cv'], 'uploads/applicants');
        $applicant = parent::store($this->getBasicColumn(['first_name','last_name','email','phone','job_title','msg' , 'cv']));
        return $applicant;       
    }


    public function applySearch(Builder $query, string $search ){
        return $query->where('first_name', "%$search%")->orWhere('last_name', "%$search%")
                      ->orWhere('email', "%$search%")->OrWhere('job_title', "%$search%");
    }

    public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    {
        return $query->orderBy($orderBy, $direction);
    }





}