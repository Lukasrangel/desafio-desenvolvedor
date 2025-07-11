<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait HttpResponse {

    public function responsePaginate(
    Paginator $paginator,
    string|int $status, 
    array|ResourceCollection $data = []) {

        return response()->json([
            'uploads' => $data,
            'paginate' => [
                'current_page' => $paginator->currentPage(),
                'peer_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
                'total_items' => $paginator->total(),
                'prevPage' => $paginator->previousPageUrl(),
                'nextPage' => $paginator->nextPageUrl()
            ],
        ], $status);
    }

    public function error(string|int $message, 
    string|int $status, 
    array $errors = [], 
    array $data = [] ) 
    {
        return response()->json([
            'message' => $message,
            'status' => $status,
            'data' => $data,
            'errors' => $errors
        ], $status);

    }

}



?>