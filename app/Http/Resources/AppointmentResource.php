<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'lawyer'     => new UserResource($this->whenLoaded('lawyer')),
            'status'     => [
                'id' => $this->status,
                'name' => $this->status_name,
            ],
            'scheduled'  => $this->scheduled_for->format('d.m.Y H:i'),
        ];
    }
}
