<?php
/** 
 * Author Keania Eric
 * Date 2020-08-03 3:10 pm
 */
namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Investor;
use App\Helpers\FinanceHandler;
use Illuminate\Console\Command;
use App\Helpers\TransactionLogger;

class MoveFundToWallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'investors:move_funds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfers an investors fund from vault to wallet';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $code  = config('unicredit.flow')['vault_to_wallet_auto_sweep'];

        $finance = new FinanceHandler(new TransactionLogger);

        $today = Carbon::now();

        Investor::active()->where('auto_invest', true)->chunk(50, function($investors) use($code, $finance, $today){

            foreach ($investors as $investor) {

                $lastInflow = Carbon::parse($investor->last_vault_inflow);
    
                $lastUpdateInHrs = $lastInflow->diffInHours($today);
    
                // if the current time is up to 48hrs
                if ( $lastUpdateInHrs >= 48) {
                   
                    // transfer fund to vault
                    $finance->handleDouble(
                        $investor, $investor, $investor->vault, $investor, 'VTW', $code
                    );
    
                    // // update next_sweep_date to null until money comes in again
                    $investor->update(['last_vault_inflow'=> null]);
                }
            }
        });
    }
}
