<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\Target;
use App\Models\Affiliate;
use App\Models\Targettable;
use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\TargetAffiliateResource;


class TargetService
{
    
    
    /**
     * Gets all targets from the database
     *
     * @return \Illuminate\Support\Facades\Collection
     */
    public function all()
    {
        return $targets = Target::all();
    }

    
    /**
     * Updates a target
     *
     * @param  \App\Models\Target $target
     * @param  array $data
     * @return void
     */
    public function update(Target $target, array $data)
    {
        return $target->update($data);
    }


    
    /**
     * Deletes a target
     *
     * @param  \App\Models\Target $target
     * @return bool
     */
    public function delete(Target $target)
    {
        Targettable::where('target_id', $target->id)->get()->each(
            function($targettable) {

                //$targettable->detach();

                $targettable->delete();
            }
        );

        return $target->delete();
    }

    
    /**
     * 
     *
     * @param   $pivot
     * @return void
     */
    public function getPercent($pivot)
    {
        $target = Target::find($pivot->target_id);

        $model = Affiliate::find($pivot->targettable_id);

        return $this->calculatePercentage($model, $target);
        
    }

    
    /**
     * Calculate the percentage target a ser met
     *
     * @param  mixed $model
     * @param  mixed $target
     * @return void
     */
    protected function calculatePercentage($model, $target)
    {
        $targetStartDate = Carbon::parse($target->created_at);

        $targetEndDate = $targetStartDate->copy()->addDays($target->days);

        $amount = 0;

        switch ($target->type) {

            case 'book_loans' : 

                $amount  = $model->loans()->whereBetween(
                    'created_at', 
                    [$targetStartDate->toDateString(), $targetEndDate->toDateString()]
                )->get()->sum('amount');

            break;

            case 'investors': 

                $investors =  $model->investors()->whereBetween(
                    'created_at', 
                    [$targetStartDate->toDateString(), $targetEndDate->toDateString()]
                )->get();
                
                $funds = 0;

                foreach ($investors as $investor) {
                    $funds += $investor->transactions()->where('code', '000')->whereBetween(
                        'created_at', 
                        [$targetStartDate->toDateString(), $targetEndDate->toDateString()]
                    )->sum('amount');
                }

                $amount  = $funds;
            break;

        }

        $percent = ($amount / $target->target) * 100;

        return $percent > 100 ? 100 : $percent;
    }

    
    /**
     * Creates a new target
     *
     * @param  array $data
     * @return void
     */
    public function create(array $data)
    {
       
        $target = $this->createTarget($data);
       
        switch ($data['category']) {

            case 'all' :
                $this->assignAllAffiliatesTarget($target);
            break;
            case 'selective' :
                $this->assignSelectiveAffiliatesTarget($target, $data['affiliates']);
            break;
        }
    }

    
    /**
     * Assign the target to all active affiliates
     *
     * @param  mixed $target
     * @return void
     */
    protected function assignAllAffiliatesTarget($target)
    {
        $affiliates = Affiliate::active()->get();

        foreach ($affiliates as $index=>$affiliate) {

            $this->assignAffiliate($affiliate, $target);
        }
    }
    
    /**
     * Assigns the target to specific targets
     *
     * @param  \App\Models\Target $target
     * @param  array $data
     * @return void
     */
    protected function assignSelectiveAffiliatesTarget($target, $data)
    {
        foreach ($data as $index=>$affiliate) {

            $this->assignAffiliate($affiliate, $target);
        }
    }

    
    /**
     * assignAffiliate
     *
     * @param  \App\Models\Affiliate $affiliate
     * @param  \App\Models\Target $target
     * @return void
     */
    protected function assignAffiliate($affiliate, $target)
    {

        $ownerId = is_object($affiliate) ? $affiliate->id : $affiliate['id'];
        return Targettable::create(
            [
                'targettable_type'=>'App\Models\Affiliate',
                'targettable_id'=> $ownerId,
                'target_id'=>$target->id
            ]
        );
    }

    
    /**
     * Creates a model
     *
     * @param  array $data
     * @return \App\Models\Target
     */
    protected function createTarget(array $data)
    {
        $expiry = Carbon::now()->addDays($data['days']);

        return Target::create(
            [
                'name'=>$data['name'],
                'days'=> $data['days'],
                'type'=> $data['type'],
                'category'=> $data['category'],
                'reward'=> $data['reward'],
                'target'=> $data['target'],
                'expires_at'=> $expiry
            ]
        );
    }

    
    /**
     * Gives an overview of the user performance
     *
     * @param  mixed $target
     * @return void
     */
    public function resolveMetrics(Target $target)
    {
        return TargetAffiliateResource::collection($target->users);
        
    }


    
    /**
     * checks 
     *
     * @param  mixed $target
     * @return void
     */
    public function checkForCompletedTarget(Target $target)
    {
        $users = $target->users;

        foreach ($users as $index=>$user) {

            if ($this->metTarget($user, $target)) {

                $this->giveTargetBonus($user, $target);
            }
        }
    }

    
    /**
     * Checks if a user met a target
     *
     * @param  mixed $user
     * @param  mixed $target
     * @return bool
     */
    public function metTarget(Model $user, $target)
    {
        
        return $this->calculatePercentage($user, $target) < 100 ? false : true;
    }
    
    /**
     * Give a user bonus
     *
     * @param  mixed $user
     * @param  mixed $target
     * @return void
     */
    protected function giveTargetBonus(Model $user, $target)
    {
        $reward  = $target->reward;

        $financeHandler =  new FinanceHandler(new TransactionLogger);

        $code = config('unicredit.flow')['target_bonus_payment'];

        $financeHandler->handleSingle(
            $user,
            'credit',
            $reward,
            $target,
            'W',
            $code  
        );

        $pivot = $user->targets->where('id', $target->id)->first()->pivot;

        $pivot->update(['met'=>true]);

    }


    
}