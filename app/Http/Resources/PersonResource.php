<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
        $image = $this->getFirstMediaUrl('person-image','small');
        return [
            "id" => $this->id,
            "name" => $this->name,
            "age" => $this->age,
            "role" => $this->role,
            "address" => $this->address,
            "country" => $this->country != null ? $this->country->toArray() : [] ,
            "image" => $image == "" ? null : $image
        ];
    }
}
