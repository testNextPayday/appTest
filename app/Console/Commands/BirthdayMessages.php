<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BirthdayService;

class BirthdayMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday:messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends birthday messages to customers daily';

    
    /**
     * Service
     *
     * @var \App\Services\BirthdayService
     */
    protected $service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BirthdayService $service)
    {
        parent::__construct();

        $this->service = $service;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->service->sendNotificationToday();
    }
}
