<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Upload;
use App\Jobs\ProcessCsvUpload;

class UploadController extends Controller
{
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
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
        $upload = Upload::create([
            'filename' => $file->getClientOriginalName(),
            'safename' => $originalName . '_' . $hash . '.' . $extension,
            'filepath' => $file->storeAs('uploads', $originalName),
            'hash'     => $hash,
            'uploaded_at' => now(),
        ]);

        ProcessCsvUpload::dispatch($upload->filepath, $upload->id);

        return response()->json(['message' => 'Arquivo enviado com sucesso!', 'upload_id' => $upload->id]);

    }
}
