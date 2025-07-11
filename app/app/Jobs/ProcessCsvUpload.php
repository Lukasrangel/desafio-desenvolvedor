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
    $filePath = storage_path("{$this->path}");
    $handle = fopen($filePath, 'r');

    // Detectar delimitador
    $firstLine = fgets($handle);
    $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
    rewind($handle);

    // Cabeçalhos esperados
    $required = ['RptDt', 'TckrSymb', 'MktNm', 'SctyCtgyNm', 'ISIN', 'CrpnNm'];
    $maxAttempts = 20;
    $header = null;
    $attempt = 0;
    $rawHeaders = [];

    // Procurar cabeçalho válido
    while (($line = fgetcsv($handle, 0, $delimiter)) !== false && $attempt < $maxAttempts) {
        $normalized = array_map('trim', $line);
        $rawHeaders[] = $normalized;

        if (count(array_intersect($required, $normalized)) === count($required)) {
            $header = $normalized;
            break;
        }

        $attempt++;
    }

    if (!$header) {
        $sample = collect($rawHeaders)->take(5)->map(fn($l) => implode($delimiter, $l))->implode("\n");
        throw new \Exception("Cabeçalho não encontrado nas primeiras {$maxAttempts} linhas. Amostras:\n{$sample}");
    }

    // Preparar inserção em lote
    $batch = [];
    $batchSize = 500;

    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
        if (count($row) !== count($header)) {
            continue;
        }

        $data = array_combine($header, $row);
        $extra = collect($data)->except($required);

        $batch[] = [
            'upload_id'     => $this->uploadId,
            'RptDt'         => date('Y-m-d', strtotime($data['RptDt'])),
            'TckrSymb'      => $data['TckrSymb'],
            'MktNm'         => $data['MktNm'],
            'SctyCtgyNm'    => $data['SctyCtgyNm'],
            'ISIN'          => $data['ISIN'],
            'CrpnNm'        => $data['CrpnNm'],
            'extra'         => json_encode($extra->all()),
            'created_at'    => now(),
            'updated_at'    => now(),
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

