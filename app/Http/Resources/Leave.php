<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Leave extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var \App\Models\Leave $leave*/
        $leave = $this;

        return [
            'id' => $leave->id,
            'start' => $leave->start,
            'end' => $leave->end,
            'status' => $leave->status,
            'created_at' => $leave->created_at,
            'updated_at' => $leave->updated_at,
        ];
    }
}
