<?php
namespace App\Traits;
trait ResponseTrait
{
    /**
     * Return a success response.
     *
     * @param mixed $data
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = null, string $message = 'Operation successful')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Return an error response.
     *
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function error(string $message, int $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code'=>$code
        ],$code);
        
    }

    /**
     * Return a success response with pagination data.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param mixed $resourceCollection
     * @param string $key
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function successPaginated($paginator, $resourceCollection, string $key = 'items', string $message = 'Operation successful')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                $key => $resourceCollection,
                'paginate' => [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                ]
            ],
        ]);
    }
}