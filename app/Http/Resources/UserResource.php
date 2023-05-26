<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $sources = $this->sourcePreferences;
        $categories = $this->categoryPreferences;
        $authors = $this->authorPreferences;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            $this->mergeWhen($sources || $categories || $authors, function () use ($sources, $categories, $authors) {
                return [
                    'preferences' => [
                        'sources' => $sources ? SourceResource::collection($sources) : [],
                        'categories' =>  $categories ? CategoryResource::collection($categories) : [],
                        'authors' =>  $authors ? AuthorResource::collection($authors) : [],
                    ],
                ];
            }),
        ];
    }
}
