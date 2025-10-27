<?php
namespace App\Services\dynamic;


class DynamicService
{
    public function validateModel($modelClass)
    {
   
        if (!class_exists($modelClass)) {
            throw new \Exception("Model {$studlyName} does not exist.");
        }

    }

    public function getQuery($request, $modelClass){
        
        
            $query = $modelClass::query();
            if ($request->has('order') && in_array($request->order, ['asc', 'desc' , 'ASC', 'DESC'])) {
                    $orderBy = $request->order_by ?? 'id';
                    $query->orderBy($orderBy, $request->order);
            }

            $columns = 'All'; 
            if ($request->has('columns') && is_array($request->columns) && !empty($request->columns)) {
                $columns = $request->columns;
            }

            
            if($request->has('type') && $request->type){
                
                $query = $query->where('type', $request->type);
            }

            if($request->has('search') && $request->search){
                
                $query->whereHas('translation', fn($q) => 
                    $q->where('title', 'LIKE', "%{$request->search}%")
                    ->orWhere('slug', 'LIKE', "%{$request->search}%")
                );
            }
            $isPaginated = false;
            if ($request->has('pagination') && $request->pagination > 0) {
                $data = $query->paginate($request->pagination);
                $isPaginated = true;
            } else {
                
                $data = $query->get();
            }



            
            
            return $this->formatResponse($data , $columns , $isPaginated);
            

          

            
    }







    public function getModelClass($model)
    {
        $studlyName = \Illuminate\Support\Str::studly($model);
        return "App\\Models\\Api\\Admin\\{$studlyName}";
    }





    public function formatResponse($data, $columns , $isPaginated)
    {

        if(!$data || $data->isEmpty()){
            return [];
        }
       

        if ($isPaginated) {
            $formattedData = [];
            foreach ($data->items() as $item) {
                $formattedData[] = $this->formatSingleRecord($item , $columns);
            }
            
            return [
                'data' => $formattedData,
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ]
            ];
        } else {
            
            $formattedData = [];
            foreach ($data as $item) {
                
                $formattedData[] = $this->formatSingleRecord($item , $columns);
            }
           
            return $formattedData;
        }
    }

    private function formatSingleRecord($record , $columns)
    {
        $formatted = [];
        if (!is_array($columns) || empty($columns)) {
          
            return  $record;
        }
        
        foreach ($columns as $column) {
            $formatted[$column] = $record->$column ?? null;
        }
        
        return $formatted;
    }


    



    
}