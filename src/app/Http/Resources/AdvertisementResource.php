<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed created_at
 * @property mixed updated_at
 * @property mixed title
 * @property mixed id
 * @property mixed offers
 */
class AdvertisementResource extends JsonResource
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
            'title' => $this->title,
            'created_at' => $this->created_at->format('c'),
            'updated_at' => $this->updated_at->format('c'),
        ];
        //If it is requested from advertisements, include relation, checking the path avoids circular cycles.
        if($request->is('api/advertisements*')){
            $defaultObj['offers'] = OfferResource::collection($this->offers);
        }
        return $defaultObj;
    }
}
