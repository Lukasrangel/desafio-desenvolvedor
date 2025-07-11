<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        'filename',
        'filepath',
        'safename',
        'hash',
        'uploaded_at'
    ];

    public function records() {
        return $this->hasMany(Record::class,'upload_id');
    }
}
