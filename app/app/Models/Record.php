<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Upload;

class Record extends Model
{

    protected $fillable = [
        'upload_id',
        'rpt_dt',
        'tckr_symb',
        'MktNm',
        'SctyCtgyNm',
        'ISIN',
        'CrpnNm',
        'extra'
    ];
    
    public function upload() {
        $this->belongsTo(Upload::class);
    }
}
