<?php
namespace App\Unicredit\Contracts;

interface Visitor
{
    
    /**
     * Takes a visitable object and just visits it
     *
     * @param  mixed $visitable
     * @return void
     */
    public function visit($visitable);
}
