<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AuthorResource;
class ArticleResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'body' => $this->body,
            'tagList' => ['hi'],
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'favorited' => false,
            'favoritesCount' => 0,
            'author' => new AuthorResource($this->user)
        ];
    }

}
