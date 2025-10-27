<?php

namespace App\Services\Admin\Gallery;

use App\Traits\HandlesImage;

class GalleryService 
{
    use HandlesImage;

    protected $model;
    protected $galleryModel;
    protected $foreignKeyField;
    protected $table;

    public function __construct($modelName)
    {
        $this->model = 'App\\Models\\Api\\Admin\\' . ucfirst($modelName);
        $this->galleryModel = 'App\\Models\\Api\\Admin\\' . ucfirst($modelName) . 'Gallery';
        $this->foreignKeyField = $modelName . '_id';
        $this->table = $modelName . '_galleries';
        
        $this->validateModels();
    }

    public static function getGallery($data){
        $service = new self($data['model']);
        $galleryModel = $service->galleryModel;

        $results = $galleryModel::where($service->foreignKeyField, $data[$service->foreignKeyField])
            ->orderBy('order', 'asc')
            ->get();
        foreach ($results as $result) {
            $result->image = $service->getImageUrl($result->image);     
        }   
        return [
            'success' => true,
            'message' => 'Gallery retrieved successfully',
            'data' => $results
        ];
        
        
    }

    /**
     * Main method to handle gallery operations
     */
    public static function storeGallery($data)
    {
        $service = new self($data['model']);

        $service->validateParentModel($data);
        
        // Update order of existing images
        if (isset($data['old_order']) && !empty($data['old_order'])) {
            $service->updateImageOrder($data['old_order']);
        }
        
        $service->deleteRemovedImages($data);
        
        if (isset($data['new_images']) && !empty($data['new_images'])) {
            $service->storeNewImages($data['new_images'], $data[$service->foreignKeyField]);
        }
        return $service->getGalleryResults($data[$service->foreignKeyField]);
    }

    /**
     * Update the order of existing gallery images
     */
    protected function updateImageOrder(array $orderData)
    {
        foreach ($orderData as $item) {
            $this->validateOrderItem($item);
            
            $gallery = $this->findGalleryItem($item['id']);
            $gallery->order = $item['order'];
            $gallery->save();
        }
    }

    /**
     * Store new images to gallery
     */
    protected function storeNewImages(array $newImages, $foreignKeyValue)
    {
        foreach ($newImages as $imageData) {
            $this->validateNewImageData($imageData);
            $this->createGalleryItem($imageData, $foreignKeyValue);
        }
    }


    protected function deleteRemovedImages($data)
    {
        $galleryModel = $this->galleryModel;
        
        if (isset($data['old_order']) && !empty($data['old_order'])) {
            $existingIds = array_column($data['old_order'], 'id');
        
            foreach ($galleryModel::where($this->foreignKeyField, $data[$this->foreignKeyField])->whereNotIn('id', $existingIds)->get() as $galleryItem) {
                $this->deleteImage($galleryItem->image);
                $galleryItem->delete();
            }
        } else {

            foreach ($galleryModel::where($this->foreignKeyField, $data[$this->foreignKeyField])->get() as $galleryItem) {
                $this->deleteImage($galleryItem->image);
                $galleryItem->delete();
            }
        
        }

        
    }


    protected function createGalleryItem(array $imageData, $foreignKeyValue)
    {
        $galleryModel = $this->galleryModel;
        $gallery = new $galleryModel();
        
        $gallery->{$this->foreignKeyField} = $foreignKeyValue;
        $gallery->image = $this->uploadImages($imageData['file'], 'uploads/' . $this->table);
        $gallery->order = $imageData['order'];
        
        if (!$gallery->save()) {
            throw new \Exception("Failed to save gallery item");
        }
        
        return $gallery;
    }

    /**
     * Get gallery results with image URLs
     */
    protected function getGalleryResults($foreignKeyValue)
    {
        $galleryModel = $this->galleryModel;
        
        $results = $galleryModel::where($this->foreignKeyField, $foreignKeyValue)
            ->orderBy('order', 'asc')
            ->get();

        foreach ($results as $result) {
            $result->image = $this->getImageUrl($result->image);
        }

        return [
            'success' => true,
            'message' => 'Gallery updated successfully',
            'data' => $results
        ];
    }

    /**
     * Upload image file
     */
    public function uploadImages($image, $upload = "uploads")
    {
        return $this->uploadImage($image, $upload);
    }

    /**
     * Get full URL for image
     */


    /**
     * Validate that required models exist
     */
    protected function validateModels()
    {
        if (!class_exists($this->galleryModel)) {
            throw new \Exception("Gallery model {$this->galleryModel} does not exist");
        }
        
        if (!class_exists($this->model)) {
            throw new \Exception("Model {$this->model} does not exist");
        }
    }

    /**
     * Validate that parent model record exists
     */
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

    /**
     * Find gallery item by ID
     */
    protected function findGalleryItem($id)
    {
        $galleryModel = $this->galleryModel;
        $gallery = $galleryModel::find($id);
        
        if (!$gallery) {
            throw new \Exception("Gallery item with ID {$id} not found");
        }
        
        return $gallery;
    }

    /**
     * Validate order item data
     */
    protected function validateOrderItem($item)
    {
        if (!isset($item['id']) || !isset($item['order'])) {
            throw new \Exception("Order item must have 'id' and 'order' fields");
        }
    }

    /**
     * Validate new image data
     */
    protected function validateNewImageData($imageData)
    {
        if (!isset($imageData['file']) || !isset($imageData['order'])) {
            throw new \Exception("New image must have 'file' and 'order' fields");
        }
    }




}