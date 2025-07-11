<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\ResourceCollection;

trait HttpResponse {

    public function responsePaginate(string|int $currentPage, string|int $peer_page, string|int $lastPage,string|int $total, string|int|null $prevPage, string|int|null $nextPage,string|int $status, array|ResourceCollection $data = []) {

        return response()->json([
            'uploads' => $data,
            'paginate' => [
                'current_page' => $currentPage,
                'peer_page' => $peer_page,
                'last_page' => $lastPage,
                'total_items' => $total,
                'prevPage' => $prevPage,
                'nextPage' => $nextPage,
            ],
        ], $status);
    }

    public function error(string|int $message, string|int $status, array $errors = [], array $data = [] ) {

        return response()->json([
            'message' => $message,
            'status' => $status,
            'data' => $data,
            'errors' => $errors
        ], $status);

    }

}



?>