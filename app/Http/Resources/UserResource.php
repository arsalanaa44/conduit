<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
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
            'user' => [
                'email' => $this->email,
                'token' => $this->token,
                'username' => $this->username,
                'bio' => $this->bio,
                'image' => $this->image // or use a default value like 'image' => $this->image ?? null,
            ]
        ];
    }
}
