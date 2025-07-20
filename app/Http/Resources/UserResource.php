<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        unset($request);

        return [
            'id' => $this->id,
            'prefixname' => $this->prefixname,
            'firstname' => $this->firstname,
            'middlename' => $this->middlename,
            'lastname' => $this->lastname,
            'suffixname' => $this->suffixname,
            'email' => $this->email,
            'photo' => $this->photo,
            'type' => $this->type,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'full_name' => $this->full_name,
            'middle_initial' => $this->middle_initial,
            'gender' => $this->gender,
            'details' => $this->whenLoaded('details', fn() => DetailResource::collection($this->details)),
        ];
    }
}
