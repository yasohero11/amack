<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $image = $this->getFirstMediaUrl('article-image');
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "type" => $this->type_id,
            "featured" => $this->featured,
            "active" => $this->active,
            "date" => $this->created_at,
            "last_updated" => $this->updated_at,
            "longlat" => $this->long . "," . $this->lat,
            "source_links" => $this->source_links,
            "location" => $this->location,
            "history_fact" => $this->history_fact,
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,
            "fact_index" => $this->fact_index ?? 0,
            "country" => $this->country,
            "era" => $this->era,
            "author" => $this->author,
            "media" => $this->getMedia('article-image')->toArray(),
            "image" => $image == "" ? null : $image
        ];
    }
}
