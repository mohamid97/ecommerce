<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Front\DynamicFilter\DynamicFilterRequest;
use App\Http\Resources\Api\Front\Data\DataResource;
use App\Models\Api\Admin\Applicant;
use App\Services\Front\Profile\FrontendService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\ResponseTrait;
class FrontendController extends Controller
{
    use ResponseTrait;
    
    public $frontend;
    public $studlyName;
    public $modelClass;
    public $resourceClass;
    public $forignKey;
    public function __construct(FrontendService $frontend)
    {    
        $this->frontend = $frontend;    
    }

    private function PrepareModel($model){

        


        $this->studlyName    = Str::studly($model);
        if($this->studlyName == 'About' || $this->studlyName == 'Contact'){
           $this->modelClass    = $this->frontend->getModel($this->studlyName . 'Us');
        }else{
           $this->modelClass    = $this->frontend->getModel($model == 'social'?'Soical': $this->studlyName);
        }
        $this->resourceClass =  "App\\Http\\Resources\\Api\\Admin\\{$this->studlyName}Resource";
    }
    public function get(Request $request){

       
        try{
            if(!$request->has('model')){              
                return $this->error(__('main.no_model') , 404);
            }
            $this->PrepareModel($request->model);
            return  $this->success($this->frontend->getQuery($this->modelClass, $this->resourceClass, $request), __('main.retrieved_successfully', ['model' => $this->studlyName]));

        }catch(\Exception $e){
            return $this->error($e->getMessage() , 500);
        }


        
        return $this->success($reponseData, __('main.stored_successfully', ['model' => $studlyName]));
        

        
        
    }



    public function dynamicFilter(DynamicFilterRequest $request){
        try{
            $this->PrepareModel($request->model);
            return  $this->success($this->frontend->dynamicFilter($this->modelClass, $this->resourceClass, $request), __('main.retrieved_successfully', ['model' => $this->studlyName]));           

        }catch(\Exception $e){
            return $this->error($e->getMessage() , 500);
        }

    }



    private function prepareGalleryModel($model){
        $this->studlyName    = Str::studly($model) . 'Gallery';
        if(class_exists("App\\Models\\Api\\Admin\\{$this->studlyName}")){
            $this->modelClass    = "App\\Models\\Api\\Admin\\{$this->studlyName}";
            $this->forignKey     = Str::snake($model) . '_id';
        }else{
            throw new \Exception("Model {$this->studlyName} does not exist.");  
        }
        
    }

    public function getGallery(Request $request){
        try{
            
            if(!$request->has('model')){              
                return $this->error(__('main.no_model') , 404);
            }

            if(!$request->has('id') || !is_numeric($request->id) ){              
                return $this->error(__('main.no_id') , 404);
            }

            $this->prepareGalleryModel($request->model);

            return  $this->success($this->frontend->getgalleries($this->modelClass, $request->id , $this->forignKey ), __('main.retrieved_gallery_successfully', ['model' => $this->studlyName]));           

        }catch(\Exception $e){
            return $this->error($e->getMessage() , 500);
        }
        
    }



        public function search(Request $request){
        try{
            if(!$request->has('model')){              
                return $this->error(__('main.no_model') , 404);
            }
            if(!$request->has('value') || $request->has('column')){              
                return $this->error(__('main.no_search') , 404);
            }
            $this->prepareModel($request->model);
            $data = $this->modelClass::whereHas('translations',function($query) use ($request){
                $query->where($request->column, 'like', '%' . $request->value . '%');
            })->get();

            return  $this->success(DataResource::collection($data), __('main.retrieved_successfully', ['model' => $this->studlyName]));           

        }catch(\Exception $e){
            return $this->error($e->getMessage() , 500);
        }
    }// end search function  








    
}