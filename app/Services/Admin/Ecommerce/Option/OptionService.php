<?php
namespace App\Services\Admin\Ecommerce\Option;

use App\Models\Api\Ecommerce\Option;
use App\Services\BaseModelService;
use App\Traits\HandlesImage;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;


class OptionService extends BaseModelService
{

    protected array $relations = ['values'];
    use StoreMultiLang , HandlesImage;
    
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
                    $imagePath = $this->uploadImage($valueData['value'], 'uploads/option_values');
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
    
    public function update($id)
    {
        // =========================================================
        // STEP 1: Update the Option itself (image, code, value_type)
        // =========================================================
        $this->uploadSingleImage(['option_image'], 'uploads/options');
        $option = parent::update($id, $this->getBasicColumn(['option_image', 'code', 'value_type']));

        // =========================================================
        // STEP 2: Update the Option title translation
        // =========================================================
        $this->processTranslations($option, $this->data, ['title']);

        // =========================================================
        // STEP 3: Sync Option Values
        // =========================================================
        if (isset($this->data['values']) && is_array($this->data['values'])) {

            $incomingIds = collect($this->data['values'])
                ->filter(fn($value) => isset($value['id']))
                ->pluck('id')
                ->toArray();
            $option->values()
                ->whereNotIn('id', $incomingIds)
                ->delete();
            foreach ($this->data['values'] as $valueData) {
                if ($this->data['value_type'] === 'image' && isset($valueData['value_image'])) {
                    $valueData['value'] = $this->uploadSingleImage($valueData['value'], 'uploads/option_values');
                } else {
                    $valueData['value'] = $valueData['value'] ?? null;
                }

                if (isset($valueData['id'])) {

                    $optionValue = $option->values()->find($valueData['id']);

                    if ($optionValue) {
                        $optionValue->update([
                            'value' => $valueData['value'],
                        ]);

                        // Update its translations (title in ar/en)
                        $this->processTranslations($optionValue, $valueData, ['title']);
                    }

                } else {

                    $optionValue = $option->values()->create([
                        'value' => $valueData['value'],
                    ]);

                    // Save its translations (title in ar/en)
                    $this->processTranslations($optionValue, $valueData, ['title']);
                }

            } // end foreach values

        } // end if values

        return $option;

    } // end update option  
     
    public function delete($id){
        $option = parent::delete($id);
        return $option;
    }


    public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    {
        return $query->orderBy($orderBy, $direction);
    }


}