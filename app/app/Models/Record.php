<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Upload;

class Record extends Model
{

    protected $fillable = [
        'upload_id',
        'RptDt',
        'TckrSymb',
        'MktNm',
        'SctyCtgyNm',
        'ISIN',
        'CrpnNm',
        'extra'
    ];

    protected $casts = [
        'extra' => 'array',
    
    ];
    
    public function upload() {
        $this->belongsTo(Upload::class);
    }
}
