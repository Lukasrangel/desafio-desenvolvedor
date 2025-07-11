<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessCsvUpload implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    use Dispatchable;

    protected $path;
    protected $uploadId;

    public function __construct(string $path, int $uploadId)
    {
        //caminho do arquivo e id do arquivo
        $this->path = $path;
        $this->uploadId = $uploadId;
    }

    public function handle()
    {
        //abre e lê arquivo csv
        $handle = fopen(storage_path("{$this->path}"), 'r');
        $header = fgetcsv($handle);

        //array que conterá 500 indices, para salvar no db em lote
        $batch = [];
        $batchSize = 500;

        //enquanto row ainda tiver bytes retorna != false
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);
            
            //campos extras serão salvos em json na tabela do banco
            $extraFields = collect($data)->except([
                'RptDt', 'TckrSymb', 'MktNm', 'SctyCtgyNm', 'ISIN', 'CrpnNm'
            ]);

            
            $batch[] = [
                'upload_id'      => $this->uploadId,
                'RptDt'         => date('Y-m-d', strtotime($data['RptDt'])),
                'TckrSymb'      => $data['TckrSymb'],
                'MktNm'         => $data['MktNm'],
                'SctyCtgyNm'   => $data['SctyCtgyNm'],
                'ISIN'           => $data['ISIN'],
                'CrpnNm'        => $data['CrpnNm'],
                'extra'          => json_encode($extraFields->all()),
                'created_at'     => now(),
                'updated_at'     => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('records')->insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('records')->insert($batch);
        }

        fclose($handle);
    }
}


