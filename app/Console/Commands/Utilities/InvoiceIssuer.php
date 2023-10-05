<?php

namespace App\Console\Commands\Utilities;

use PDF, Mail;

use Carbon\Carbon;
use App\Mail\PaymentDue;
use App\Models\RepaymentPlan;
use Illuminate\Console\Command;
use App\Unicredit\Collection\Utilities;

class InvoiceIssuer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'issue:invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Issues invoices for due loans';

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
          // where payday is in two days from now
          $today = Carbon::now();

          $payday = $today->addDays(2)->toDateString();

          $plans = RepaymentPlan::has('loan')->where('status',false)->where('payday',$payday)->get();

        

        foreach($plans as $repaymentPlan) {
            
            $data = $repaymentPlan->getInvoiceData();
            
            $pdf = PDF::loadView('pdfs.repayment_invoice', $data);
            
            $storePath = "pdfs/invoices/" . $data['invoiceNumber'] . ".pdf";
            
            $invoiceLink = public_path() . '/storage/' . $storePath;
            
            $pdf->save($invoiceLink);

        
        
            if($repaymentPlan->update(['invoice' => "public/$storePath"])) {
                
                Mail::to($data['user']->email)->send(new PaymentDue($invoiceLink, $data['user'], $repaymentPlan));
            }
        }
    }
}
