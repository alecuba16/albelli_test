<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $defaultObj=[
        'id' => $this->id,
        'product_name' => $this->product_name,
        'discount_value' => (int)$this->discount_value,
        'start_date' => $this->start_date->format('c'),
        'end_date' => $this->end_date->format('c'),
        'created_at' => $this->created_at->format('c'),
        'updated_at' => $this->updated_at->format('c'),
        ];
        //If it is requested from offers, include relation, checking the path avoids circular cycles.

        if( $request->is('api/offers*')){
            $defaultObj['advertisements'] = AdvertisementResource::collection($this->advertisements);
        }
        return $defaultObj;
    }
}
