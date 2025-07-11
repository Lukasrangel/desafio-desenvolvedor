<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Upload;
use App\Jobs\ProcessCsvUpload;
use App\Http\Resources\v1\UploadResource;
use App\Traits\HttpResponse;


class UploadController extends Controller
{
    use HttpResponse;


    public function store(Request $request){

    
        $validator = Validator::make($request->only('file'), [
            'file' => 'required|file|mimes:csv,xlsx',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


        $file = $request->file('file');
        $hash = hash_file('sha256', $file->getRealPath());

        // Verifica se já existe um arquivo com esse hash
        if (Upload::where('hash', $hash)->exists()) {
            return response()->json(['message' => 'Este arquivo já foi enviado.'], 422);
        }

        // Salva o arquivo na tabela e no path storage/uploads
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $safeName = pathinfo($originalName, PATHINFO_FILENAME) . '_' . $hash . '.' . $extension;
        $storedPath = $file->storeAs('uploads', $safeName);
        $absolutePath = 'app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $safeName;

        $upload = Upload::create([
            'filename' => $file->getClientOriginalName(),
            'safename' => $safeName,
            'filepath' => $storedPath,
            'hash'     => $hash,
            'uploaded_at' => now(),
        ]);

        ProcessCsvUpload::dispatch($absolutePath, $upload->id);
        
        return response()->json(['message' => 'Arquivo enviado com sucesso!', 'upload_id' => $upload->id]);

    }

    public function show() {

        $uploads = Upload::paginate(10);
        $data = UploadResource::collection($uploads);
        
        return $this->responsePaginate($uploads->CurrentPage(), $uploads->perPage(), $uploads->lastPage(), $uploads->total(),$uploads->previousPageUrl(),$uploads->nextPageUrl(), 200, $data);
    }
}
