<?php

namespace App\Console\Commands;

use App\Models\PromissoryNote;
use Illuminate\Console\Command;

class PromissoryNoteInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promissory:interest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pays promissory investors their monthly commission';

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
        //
        PromissoryNote::active()->chunk(100, function($notes){

            foreach($notes as $note) {

                if ($note->isDayAnniversary()) {
    
                    $note->service->monthlyTask();
                }
                
            }
        });

        
    }
}
