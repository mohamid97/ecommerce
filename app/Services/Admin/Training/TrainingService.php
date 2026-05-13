<?php

namespace App\Services\Admin\Training;

use App\Services\BaseModelService;
use App\Models\Api\Admin\Training as TrainingModel;

class TrainingService extends BaseModelService
{
    protected string $modelClass = TrainingModel::class;
}
