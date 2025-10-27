<?php
namespace App\Services\Front\Profile;

use App\Http\Resources\Api\Front\Gallery\GalleryResource;

class FrontendService{


    private $reponseData = [];

    public function getModel($studlyName){
        $modelClass = "App\\Models\\Api\\Admin\\{$studlyName}";

        if (!class_exists($modelClass)) {
            throw new \Exception("Model {$studlyName} does not exist.");
        }

        return $modelClass;
    }

    public function getQuery($modelClass, $resourceClass , $request){


        if($request->has('relations') && is_array($request->relations)){

            // need to ckeck if model has this relations
            foreach($request->relations as $relation){
                if(!method_exists($modelClass , $relation)){
                    throw new \Exception("Relation {$relation} does not exist in model.");
                }
            }

          $query = $modelClass::with($request->relations);

        }else{

          $query = $modelClass::query();

        }
        if ($request->has('order') && in_array($request->order, ['asc', 'desc' , 'ASC', 'DESC'])) {
                    $orderBy = $request->order_by ?? 'id';
                    $query->orderBy($orderBy, $request->order);
        }


        if ($request->has('pagination') && $request->pagination > 0) {
            $query = $query->paginate($request->pagination);
            $this->createResponsePaginate($query);

        } else {
            if($request->has('id')){
                $query = $query->where('id' , $request->id)->get();
            }else{
              $query = $query->get();
            }


        }

        $this->createResponseItem($query , $resourceClass);



        return $this->reponseData;
    }

    private function createResponsePaginate($query){
           $this->reponseData['pagination'] =
            [
                        'current_page' => $query->currentPage(),
                        'last_page' => $query->lastPage(),
                        'per_page' => $query->perPage(),
                        'total' => $query->total(),
            ];
    }


    private function createResponseItem($query , $resourceClass){
         $this->reponseData['item'] = $resourceClass::collection($query);
    }



  // start dynamic  filter

  public function dynamicFilter($modelClass, $resourceClass , $request){
    $query = $modelClass::query();
    // apply filters
    foreach($request->filter as $filter){
        if(!isset($filter['column']) || !isset($filter['value'])){
            throw new \Exception("Filter must have column and value.");
        }
        $column = $filter['column'];
        $value  = $filter['value'];
        $table  = (new $modelClass)->getTable();

        // Ensure column exists
        if (!\Schema::hasColumn($table, $column)) {
            throw new \Exception("Column {$column} does not exist in model.");
        }

        // Detect column type
        $columnType = \DB::getSchemaBuilder()->getColumnType($table, $column);

        // If JSON column → use whereJsonContains
        if ($columnType === 'json' || in_array($column, ['position'])) {
            $query->whereJsonContains($column, $value);
        } else {
            $query->where($column, $value);
        }
    }

    if ($request->has('order') && in_array($request->order, ['asc', 'desc' , 'ASC', 'DESC'])) {
                $orderBy = $request->order_by ?? 'id';
                $query->orderBy($orderBy, $request->order);
    }
    if ($request->has('pagination') && $request->pagination > 0) {
        $query = $query->paginate($request->pagination);
        $this->createResponsePaginate($query);

    }else{
        $query = $query->get();
    }

    $this->createResponseItem($query , $resourceClass);
    return $this->reponseData;


  } // end dynamic filter


  // get galleries
  public function getgalleries($modelClass , $id , $forignKey){
    $query = $modelClass::where($forignKey , $id)->get();

    return GalleryResource::collection($query);



  }




}
