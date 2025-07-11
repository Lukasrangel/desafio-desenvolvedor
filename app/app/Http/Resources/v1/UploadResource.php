<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UploadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'fileName' => $this->filename,
            'dateOfSend' => Carbon::parse($this->uploaded_at)->format('d/m/Y H:i:s')
        ];
    }
}
