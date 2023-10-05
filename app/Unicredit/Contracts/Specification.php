<?php
namespace App\Unicredit\Contracts;

use Illuminate\Database\Eloquent\Model;


interface Specification
{
    
    /**
     *  Checks if a user satisfies a particular spec
     *
     * @param \Illuminate\Database\Eloquent\Model $user
     * @return bool
     */
    public function isSatisfiedBy(Model $user) : bool;
}