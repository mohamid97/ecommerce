<?php

namespace App\Services\Front\Applicant;

use App\Models\Api\Admin\Applicant;

class ApplicantService{
    public function StoreApplicant($request){
        return Applicant::create($request->all());
    }
    public function updateApplicant($request){
        return Applicant::update($request->all());

    }
}