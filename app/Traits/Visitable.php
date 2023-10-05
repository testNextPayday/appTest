<?php
namespace App\Traits;
use App\Unicredit\Contracts\Visitor;


/**  This is a simple trait for classes that allows the implementation of the visitors design pattern */
trait Visitable
{

        
    /**
     * Every class that can be visited accepts a visitor
     *
     * @param  mixed $visitor
     * @return void
     */
    public function accept(Visitor $visitor)
    {
        $visitor->visit($this);
    }
}