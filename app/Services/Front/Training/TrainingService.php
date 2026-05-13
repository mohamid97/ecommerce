<?php

namespace App\Services\Front\Training;

use App\Models\Api\Admin\Training;

class TrainingService
{
    public function store(array $data): Training
    {
        return Training::create($data);
    }
}
