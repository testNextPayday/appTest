<?php
namespace App\Collection;

/**
 * A factory class to simply generate logs for collection s
 */
class CollectionLogFactory
{
     /**
     * Get the log data for penalty
     *
     * @param  mixed $loan
     * @param  mixed $amount
     * @return void
     */
    public static function getCollectionLogData($loan, $amount, $desc, $direction, $reference, $date, $method=null)
    {
        
        return [
            'loan_id'=> $loan->id,
            'user_id'=> $loan->user->id,
            'reference'=> $reference,
            'amount'=> $amount,
            'direction'=> $direction,
            'code'=> '036',
            'status'=> 2,
            'collection_date'=>$date ?? now(),
            'description'=> $desc,
            'collection_method'=> $method ?? 'Wallet Transaction',
        ];
    }


    /**
     * Get the log data for penalty
     *
     * @param  mixed $loan
     * @param  mixed $amount
     * @return void
     */
    public static function getPenaltyDataLog($loan, $amount, $desc, $direction, $reference, $date)
    {
        
        return [
            'loan_id'=> $loan->id,
            'user_id'=> $loan->user->id,
            'reference'=> $reference,
            'amount'=> $amount,
            'direction'=> $direction,
            'code'=> '036',
            'status'=> 2,
            'collection_date'=>$date ?? now(),
            'description'=> $desc,
            'collection_method'=> 'Penalty',
            'is_penalty'=> true
        ];
    }


        /**
     * Get Log Data
     *
     * @param  mixed $user
     * @return void
     */
    public static function getLogData($user, $direction, $desc, $entity, $reference, $amount)
    {
        return [
            'plan_id'=> $entity->id,
            'loan_id'=> $entity->loan->id,
            'user_id'=> $user->id,
            'reference'=> $reference,
            'amount'=> $amount,
            'direction'=> $direction,
            'code'=> '036',
            'status'=> 2,
            'collection_date'=>now(),
            'description'=> $desc,
            'collection_method'=> 'Wallet'
        ];
    }


    
    /**
     * Generates the topup or settlement log data
     *
     * @param  mixed $loan
     * @param  mixed $collectMode
     * @return void
     */
    public static function getSettlementOrTopupLogData($loan, $collectMode,$reference, $amount, $obj=null)
    {
        return [
            'loan_id'=> $loan->id,
            'user_id'=> $loan->user->id,
            'reference'=> $reference,
            'amount'=> $amount,
            'direction'=> 2,
            'code'=> '036',
            'status'=> 2,
            'collection_date'=>now(),
            'description'=> 'Flow from loan wallet to Repayment Plan',
            'collection_method'=> 'Wallet',
            'is_settlement'=> $obj instanceof \App\Models\Settlement ? true : false,
            'settlement_id'=> $obj instanceof \App\Models\Settlement ? $obj->id : null,
            'is_topup'=> $obj instanceof \App\Models\Loan ? true : false
        ];
    }

}