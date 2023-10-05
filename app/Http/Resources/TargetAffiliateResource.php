<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Services\TargetService;
use Illuminate\Http\Resources\Json\JsonResource;

class TargetAffiliateResource extends JsonResource
{
    
    protected $targetService;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->targetService = new TargetService();

        return [
            'name'=> $this->name,
             'percentage'=> round($this->targetService->getPercent($this->pivot), 2),
             'reward'=> $this->reward,
             'days'=> $this->days,
             'timeLeft'=> $this->getTimeLeft($this->created_at, $this->days),
             'type'=> $this->type,
             'target'=> $this->target
        ];
    }

    
    /**
     * Time left on target
     *
     * @param  Date $startDate
     * @param  int $days
     * @return array
     */
    protected function getTimeLeft($startDate, $days)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = $startDate->copy()->addDays($days);

        return ['startTime'=> $startDate->toDateString(), 
                'endTime'=>$endDate->toDateString()
            ];
    }
}
