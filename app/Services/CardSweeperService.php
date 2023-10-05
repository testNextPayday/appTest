<?php
namespace App\Services;

/**
 * Handles all dashboard sweeps
 */
class CardServiceSweeper 
{

    /**
     * @var \App\Unicredit\Collection\CardService
     */
    protected $service;

    
    /**
     * Initialise our sweeper
     *
     * @param  \App\Unicredit\Collection\CardService $sweeper
     * @return void
     */
    public function __construct(CardService $sweeper)
    {

        $this->service = $sweeper;
    }
}