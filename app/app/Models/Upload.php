<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        'filename',
        'filepath',
        'hash',
        'uploaded_at'
    ];

    public function record() {
        $this->hasMany(Record::class);
    }
}
