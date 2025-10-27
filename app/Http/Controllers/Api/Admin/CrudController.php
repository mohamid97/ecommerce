<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Gallery\StoreGallery;
use Illuminate\Http\Request;
use App\Http\Requests\ModelRequestFactory;
use App\Services\Admin\Gallery\GalleryService;
use App\Services\Admin\Specification\SpecificationService;
use App\Services\ModelServiceFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrudController extends Controller
{
    use \App\Traits\ResponseTrait;
    
    protected $data;

    protected function getResourceClass(string $modelName  , $msg = 'main.stored_successfully')
    {

      
        $studlyName = Str::studly($modelName);
        
        $resourceClass =  "App\\Http\\Resources\\Api\\Admin\\{$studlyName}Resource";

        if (class_exists($resourceClass) && (!empty($this->data))) {
            if($this->data instanceof \Illuminate\Pagination\AbstractPaginator){
                return $this->success(
                    [
                    'items'=>$resourceClass::collection($this->data) ,
                    'pagination'=>[      
                        'current_page' => $this->data->currentPage(),
                        'last_page' => $this->data->lastPage(),
                        'per_page' => $this->data->perPage(),
                        'total' => $this->data->total(),
                        ]
                    ],
                    __($msg, ['model' => $modelName])
                );
             }
    
            return $this->success( is_array($this->data)|| $this->data instanceof \Illuminate\Support\Collection  ? $resourceClass::collection($this->data) : new $resourceClass($this->data), __($msg, ['model' => $modelName]));
        }
        return $this->error( __('main.no_model_data', ['model' => $modelName]) , 404);

        
    }

    public function all(Request $request)
    {
        
        try {
            $service = ModelServiceFactory::make($request->model);
            $this->data = $service->all($request->all());
            return $this->getResourceClass($request->model, 'main.list_successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function view(Request $request)
    {
        
        try {
            $service = ModelServiceFactory::make($request->model);
            $this->data = $service->view($request->id);
            return $this->getResourceClass($request->model , 'main.model_details');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

  
    public function store(Request $request)
    {
       
        ModelRequestFactory::validate($request->model, 'store', $request);
       
        try{
            $service = ModelServiceFactory::make($request->model);
            DB::beginTransaction();
            $this->data = $service->setData($request->except('model'))->store();
            DB::commit();
            return  $this->getResourceClass($request->model);
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage() , 500);
        }

        

    }



    public function update(Request $request)
    {
        ModelRequestFactory::validate($request->model, 'update', $request);
        try{
            
            $service = ModelServiceFactory::make($request->model);
            DB::beginTransaction();
    
            $this->data  = $service->setData($request->except(['model', 'id']))->update($request->id);
            DB::commit();
          
            return  $this->getResourceClass($request->model);
        }catch(\Exception $e){
          
            DB::rollBack();
            return $this->error($e->getMessage() , 500);
        }


    }

    public function delete(Request $request)
    {
        if(!$request->id){
            return $this->error(__('main.no_id_exist') , 404);
        }
        try{
            DB::beginTransaction();
            $service = ModelServiceFactory::make($request->model);
            $service->delete($request->id);
            DB::commit();
            return $this->success(null , __('main.deleted_successfully'));
        }catch(\Exception $e){
             DB::rollBack();
            return $this->error($e->getMessage() , 500);
        }


    }


    public function storeGallery(Request $request)
    {
        try {
            DB::beginTransaction();
            $response = GalleryService::storeGallery($request->all());
            DB::commit();
            return $response;
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }

        
    }

    public function viewGallery(Request $request)
    {
        try {
            $galleries = GalleryService::getGallery($request->all());
            return $galleries;
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }


    public function storeSpecification(Request $request){
        try{
            DB::beginTransaction();
            $response = SpecificationService::storeSpecification($request->all());
            DB::commit();
            return $response;
            
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }



    public function viewSpecification(Request $request)
    {
        try {
            $specifications = SpecificationService::getSpecification($request->all());
            return $specifications;
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }


    public function handleAction(Request $request)
    {
        $service = ModelServiceFactory::make($request->model);
        $action = $request->action;
        $id = $request->id ?? null;
        $data = $request->except(['model', 'action', 'id']);

        if (!method_exists($service, $action)) {
            return response()->json(['error' => "Action '{$action}' not defined for model '{$request->model}'"], 400);
        }

        $result = $id ? $service->$action($id, $data) : $service->$action($data);
        return response()->json(['data' => $result]);
    }


    


    
}