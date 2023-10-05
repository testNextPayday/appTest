<?php 
namespace App\Unicredit\Managers;

use App\Unicredit\Collection\CollectionService;


class RestructureManager

{
    public function __construct(CollectionService $collectionService)
    {
        $this->loan = request()->loan;
        $this->duration = request()->duration;
        $this->service = $collectionService;
        
    }


    public function restructure()
    {
        $this->clearOutStandingPlans();
        $this->service->setupArmotizedRepayments($this->loan);
        $this->sendOutNotifications();
    }


    protected function clearOutStandingPlans()
    {
        return $this->loan->where('status',false)->get()->each(function($item,$value){
            $item->delete();
        });
    }

    protected function sendOutNotifications()
    {
        //$this->loan->user->notify(new LoanRestructureNotification($this->loan));
    }



}

?>