<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRatingResource extends JsonResource
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
            'id' => $this->id,
            'vk_id' => $this->vk_id,
            'avatar_url' => $this->avatar_url,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'score' => $this->score,
            'auth_user' => $this->when($this->id == auth()->user()->id, true, false),
        ];
    }
}
