<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Applicant\ApplicantStoreRequest;
use App\Models\Api\Admin\Applicant;
use App\Services\Front\Applicant\ApplicantService;
use App\Traits\ResponseTrait;

class ApplicantController extends Controller
{
    use ResponseTrait;
    public $applicant;
    public function __construct(ApplicantService $applicant)
    {
        $this->applicant = $applicant;
        
    }

    public function store(ApplicantStoreRequest $request){
        if(Applicant::where('email' , $request->email)){
            $this->applicant->StoreApplicant($request);
            return $this->success(null , __('main.stored_successfully' , ['model'=>'Applicant']));
        }
        $this->applicant->updateApplicant($request);
        return $this->success(null , __('main.update_successfully' , ['model'=>'Applicant']));
        

    }


    


}
