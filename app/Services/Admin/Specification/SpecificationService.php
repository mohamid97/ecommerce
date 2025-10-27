<?php
namespace App\Services\Admin\Specification;

use App\Models\Api\Admin\Lang;

class SpecificationService{
    protected $model;
    protected $SpecificationModel;
    protected $foreignKeyField;
    protected $table;
    protected $langs;

    public function __construct($modelName)
    {
        $this->model = 'App\\Models\\Api\\Admin\\' . ucfirst($modelName);
        $this->SpecificationModel = 'App\\Models\\Api\\Admin\\' . ucfirst($modelName) . 'Specification';
        $this->foreignKeyField = $modelName . '_id';
        $this->table = $modelName . '_specifications';
        $this->langs = Lang::all();
        
        $this->validateModels();
    }



    public static function getSpecification($data){
        $service = new self($data['model']);
        $specificationModel = $service->SpecificationModel;

    
        $results = $specificationModel::where($service->foreignKeyField, $data[$service->foreignKeyField])
            ->orderBy('id', 'asc')
            ->get(); 

        $data = [];
        foreach($results as $result){
         $data[] = [
            'id' => $result->id,
            'details'=>$service->getLangSpecification($result)
         ];
        }


        return [
            'success' => true,
            'message' => 'Specification retrieved successfully',
            'data' => $data
        ];


        
        
        
    }





    public static function storeSpecification($data)
    {
        $service = new self($data['model']);

        $service->validateParentModel($data);
        
        $service->deleteRemovedSpecification($data);
               

        if (isset($data['new_specification']) && !empty($data['new_specification'])) {
             
            $service->storeNewSpecification($data['new_specification'], $data[$service->foreignKeyField]);
        }
        return $service->getGalleryResults($data[$service->foreignKeyField]);
    }


    protected function getGalleryResults($foreignKeyValue)
    {
        $specificationModel = $this->SpecificationModel;
            
        $results = $specificationModel::where($this->foreignKeyField, $foreignKeyValue)
            ->orderBy('id', 'asc')
            ->get();

        $data = [];
        foreach($results as $result){
         $data[] = [
            'id' => $result->id,
            'details'=>$this->getLangSpecification($result)
         ];
        }


        return [
            'success' => true,
            'message' => 'Specification updated successfully',
            'data' => $data
        ];

    }

    protected function getLangSpecification($result){
        $data = [];
        foreach($this->langs as $lang){
            if($result->translate($lang->code)->prop && $result->translate($lang->code)->value ){
                $data['prop'][$lang->code] = $result->translate($lang->code)->prop;
                $data['value'][$lang->code] = $result->translate($lang->code)->value;
            }else{
                $data['prop'][$lang->code] =  null;
                $data['value'][$lang->code] = null;
            }
        }

        return $data;
    }


    protected function storeNewSpecification(array $newSpecifications, $foreignKeyValue)
    {
        foreach ($newSpecifications as $specificationData) {
           
            $this->validateNewSpecificationData($specificationData);
            $this->createSpecificationItem($specificationData, $foreignKeyValue);
        }
    }


    protected function createSpecificationItem(array $specificationData, $foreignKeyValue)
    {
        $specificationModel = $this->SpecificationModel;
        $specification = new $specificationModel();
        
        $specification->{$this->foreignKeyField} = $foreignKeyValue;
        foreach($this->langs as $lang){
            $specification->{'prop:'.$lang->code} = $specificationData['prop'][$lang->code];
            $specification->{'value:'.$lang->code} = $specificationData['value'][$lang->code];

        }
        
        if (!$specification->save()) {
            throw new \Exception("Failed to save Specification item");
        }
        
        return $specification;
    }




    protected function validateNewSpecificationData($specificationData)
    {
        foreach($this->langs as $lang){
            if (!isset($specificationData['prop'][$lang->code]) || !isset($specificationData['value'][$lang->code])) {
                throw new \Exception("New Specification must have 'prop' and 'value' fields in lang:${lang}");
            }
        }

    }



    protected function deleteRemovedSpecification($data)
    {
        $specificationModel = $this->SpecificationModel;
        
        if (isset($data['old_specification']) && !empty($data['old_specification'])) {
            $existingIds = array_column($data['old_specification'], 'id');
        
            foreach ($specificationModel::where($this->foreignKeyField, $data[$this->foreignKeyField])->whereNotIn('id', $existingIds)->get() as $specificationItem) {
                $specificationItem->delete();
            }
        } else {

            foreach ($specificationModel::where($this->foreignKeyField, $data[$this->foreignKeyField])->get() as $specificationItem) {
                $specificationItem->delete();
            }
        
        }

        
    }



    protected function validateModels()
    {
        if (!class_exists($this->SpecificationModel)) {
            throw new \Exception("Gallery model {$this->SpecificationModel} does not exist");
        }
        
        if (!class_exists($this->model)) {
            throw new \Exception("Model {$this->model} does not exist");
        }
    }


    protected function validateParentModel($data)
    {
        if (!isset($data[$this->foreignKeyField])) {
            throw new \Exception("Foreign key field {$this->foreignKeyField} is required");
        }
        
        $model = $this->model;
        if (!$model::find($data[$this->foreignKeyField])) {
            throw new \Exception("Record with ID {$data[$this->foreignKeyField]} not found in {$this->model}");
        }
    }





}