<?php
namespace App\Unicredit\Managers;

use Exception;
use App\Models\Investor;
use App\Models\Settings;
use App\Helpers\FinanceHandler;
use App\Unicredit\Logs\DatabaseLogger;
use App\Unicredit\Exceptions\NoSettingsAvailableException;
use App\Unicredit\Exceptions\ZeroPortfolioBalanceException;

class PortfolioManager 

{

    protected $percentage;

    protected $code;

    public function __construct(FinanceHandler $financeHandler )
    {
        $this->financeHandler = $financeHandler;
        $this->dbLogger = new DatabaseLogger();
        $this->code = config('unicredit.flow')['portfolio_management_fee'];
    }


    public function issuePortfolioManagementCollection()
    {
        $percentage = Settings::PortfolioManagementPercentage();

        if($percentage == 0){
            throw new NoSettingsAvailableException('No fees set for portfolio management');
        } 

        $this->percentage = $percentage/100;
        $activeInvestors = Investor::active()->get();
        foreach($activeInvestors as $investor){
            try{

                $this->collectManagementFee($investor);

            }catch(Exception $e){
                
                $this->reportCollectionException($e);
                continue;
            }
            
        }
    }


    protected function collectManagementFee(Investor $investor)
    {
        $portfolio = $investor->portfolioSize();

        if($portfolio < 1){

            throw new ZeroPortfolioBalanceException($message = "Found a zero portfolio balance for {$investor->name} on ".now()->toDateString(),$code = 0, $previous = Null,$params = $investor);
        }
        $charge = $this->percentage * $portfolio;
        $this->financeHandler->handleSingle(
            $investor,
            'debit',
            $charge,
            $investor,
            'W',
            $this->code
        );
    }


    protected function reportCollectionException(Exception $e)
    {
        $this->dbLogger->log($e->getCustomParams(),[
            'title'=>'PortFolio Management',
            'message'=>$e->getMessage(),
            'description'=>' Attempted Portfolio Management Collection',
            'status'=>0
        ]);
    }
}
?>