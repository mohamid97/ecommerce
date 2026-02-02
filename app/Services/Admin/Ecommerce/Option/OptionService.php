<?php
namespace App\Services\Admin\Ecommerce\Option;

use App\Models\Api\Ecommerce\Option;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;


class OptionService extends BaseModelService
{

    protected array $relations = ['values'];
    use StoreMultiLang;
    
    protected string $modelClass = Option::class;
    public function all($request){
        $allDetails = parent::all($request);
        return $allDetails;
    }

    public function view($id){
        $option = parent::view($id);
        return $option;
    }

    public function store()
    {
     
        $this->uploadSingleImage(['option_image'], 'uploads/options'); 
        $option = parent::store($this->getBasicColumn(['option_image','code','value_type']));
        $this->processTranslations($option, $this->data, ['title']);
        
        if (isset($this->data['values']) && is_array($this->data['values'])) {
            foreach ($this->data['values'] as $valueData) {
                // check if value image or string
                if($this->data['value_type'] == 'image' && isset($valueData['value'])){
                    // upload image
                    $imagePath = $this->uploadSingleImage($valueData['value'], 'uploads/option_values');
                    $valueData['value'] = $imagePath;
                } else {
                    $valueData['value'] = $valueData['value'] ?? null;
                }
                // Create the OptionValue
                $optionValue = $option->values()->create([
                    'value' => $valueData['value'] ?? null,
                ]);

                // Process translations for OptionValue
                $this->processTranslations($optionValue, $valueData, ['title']);
            }
        }
        return $option;
        
    }
    
    public function update($id){
        $this->uploadSingleImage(['option_image'], 'uploads/options'); 
        $option = parent::update($id , $this->getBasicColumn( ['option_image','code','value_type']));
        $this->processTranslations($option, $this->data, ['title' ]);

        // Update Option Values
        if (isset($this->data['values']) && is_array($this->data['values'])) {
            foreach ($this->data['values'] as $valueData) {
                if (isset($valueData['id'])) {
                    // Update existing OptionValue
                    $optionValue = $option->values()->find($valueData['id']);
                    
                    if (isset($optionValue)) {
                        // check if value image or string
                        if($this->data['value_type'] == 'image' && isset($valueData['value'])){
                            // upload image
                            $imagePath = $this->uploadSingleImage($valueData['value'], 'uploads/option_values');
                            $valueData['value'] = $imagePath;
                        } else if($this->data['value_type'] != 'image' && isset($valueData['value'])) {
                            $valueData['value'] = $valueData['value'] ?? null;
                        }
                        $optionValue->update([
                            'value' => $valueData['value'] ?? null,
                        ]);
                        // Process translations for OptionValue
                        $this->processTranslations($optionValue, $valueData, ['title']);
                    }
                } else {
                    // Create new OptionValue
                    if($this->data['value_type'] == 'image' && isset($valueData['value_image'])){
                        // upload image
                        $imagePath = $this->uploadSingleImage($valueData['value'], 'uploads/option_values');
                        $valueData['value'] = $imagePath;
                    } else {
                        $valueData['value'] = $valueData['value'] ?? null;
                    }
                    $optionValue = $option->values()->create([
                        'value' => $valueData['value'] ?? null,
                    ]);
                    // Process translations for OptionValue
                    $this->processTranslations($optionValue, $valueData, ['title']);
                }
            }
        }
        return $option;        
    }   
     
    public function delete($id){
        $option = parent::delete($id);
        return $option;
    }


    public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    {
        return $query->orderBy($orderBy, $direction);
    }


}