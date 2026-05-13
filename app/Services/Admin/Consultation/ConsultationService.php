<?php

namespace App\Services\Admin\Consultation;

use App\Services\BaseModelService;
use App\Models\Api\Admin\Consultation as ConsultationModel;

class ConsultationService extends BaseModelService
{
    protected string $modelClass = ConsultationModel::class;
}
