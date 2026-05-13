<?php

namespace App\Services\Front\Consultation;

use App\Models\Api\Admin\Consultation;

class ConsultationService
{
    public function store(array $data): Consultation
    {
        return Consultation::create($data);
    }
}
