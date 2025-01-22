<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PromocodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'promocode' => $this->promocode,
            'use_count' => $this->use_count, // всего
            'use_max' => $this->use_max, // на юзера
            'amount' => $this->amount,
            'valid_till' => $this->valid_till,
        ];
    }
}
