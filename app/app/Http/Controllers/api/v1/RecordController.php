<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Record;
use App\Models\Upload;
use App\Http\Resources\v1\RecordResource;
use App\Traits\HttpResponse;

class RecordController extends Controller
{

    use HttpResponse;

    public function search(Request $request, $safeName) {

        if(!$request->filled('RptDt') && !$request->filled('TckrSymb')) {

            $upload = Upload::where('safename', $safeName)
                ->with('records') 
                ->firstOrFail();

            $records = $upload->records()->paginate(20);
            $data = RecordResource::collection($records);

            return $this->responsePaginate($records, 200, $data);
        }

        $upload = Upload::where('safename', $safeName)->firstOrFail();
        $query = $upload->records()->newQuery();

        if ($request->filled('RptDt')) {
            $query->where('RptDt', 'like', '%' . $request->RptDt . '%');
        }

        if ($request->filled('TckrSymb')) {
            $query->where('TckrSymb', 'like', '%' . $request->TckrSymb . '%');
        }

        $records = $query->paginate(20);
        $data = RecordResource::collection($records);

        return $this->responsePaginate($records,200,$data);
    }
}
