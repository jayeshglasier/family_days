<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helper\ProjectHelper;
use Carbon\Carbon;

class ChildListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resultCollection = $this->collection->transform(function ($row) 
        {
            return [
                "child_id"      => $row->user_id,
                "full_name"     => $row->full_name ? $row->full_name:'',
            ];
        });
        return $resultCollection;
    }
}
