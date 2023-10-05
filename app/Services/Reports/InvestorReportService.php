<?php
namespace App\Services\Reports;

use App\Models\Investor;
use App\Models\WalletTransaction;



/**
 * Handles details of investor 
 */
class InvestorReportService
{
    
  
    protected $fromDate;

    protected $toDate;

    protected $activity;

    protected $data;

    
    /**
     * investor
     *
     * @var \App\Models\Investor
     */
    protected $investor;
    
    /**
     * Initiate
     *
     * @param  mixed $data
     * @return void
     */
    public function __construct(array $data)
    {

        $this->data = $data;

        

        $this->investorId  = $data['investor'];
        

        $this->fromDate = $data['from'];

        $this->toDate = $data['to'];

        $this->activity = $data['activity'];

        
    }

    
    /**
     * Public interface to acess results
     *
     * @return collect
     */
    public function getResult() 
    {
        $output = $this->buildQuery();

        return $output;
    }

    
    
    /**
     * Builds the report query
     *
     * @return collection
     */
    protected function buildQuery()
    {
    
      
        $query = new WalletTransaction;

        $query = isset($this->investorId) ? $query->where('owner_id', $this->investorId) : $query;
        
        $query = $query->whereBetween('created_at', [$this->fromDate, $this->toDate]);

        // Recoveries previously used wallet and now vault
        $query = $this->activity == '012' ? $query->whereIn('purse', [1, 2]) : $query->where('purse', 1);

        $query = isset($this->activity) ? $query->where('code', $this->activity) : $query;
        // dump($query->toSql());
        // dd($query->getBindings());
        $response = $query->with('owner')->get(
            ['id', 'amount', 'description', 'reference', 'code','direction', 'created_at', 'owner_type', 'owner_id', 'cancelled']
        );
       
        return $response;


    }


}