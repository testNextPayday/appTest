<?php
namespace App\Structures;

use App\Models\User;
use App\Models\Staff;
use App\Models\Investor;
use App\Models\Affiliate;


class BirthdayPerson 
{
    
    /**
     * Initialize a person object
     *
     * @param  $birthday
     * @return void
     */
    public function __construct($birthday)
    {
        $this->data = $birthday;
    }

    
    /**
     * Returns the model for sending birthdays
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function user() 
    {
        return $this->getUser();
    }

    
    /**
     * 
     * Retrieves the user based on model attribute
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function getUser()
    {
        // switch ($this->data['model']) {

        //     case 'Borrower':
        //         $user = User::find($this->data['id']);
        //     break;
        //     case 'Investor': 
        //         $user = Investor::find($this->data['id']);
        //     break;
        //     case 'Staff':
        //         $user = Staff::find($this->data['id']);
        //     break;
        //     case 'Affiliate': 
        //         $user = Affiliate::find($this->data['id']);
        //     break;
        //     default :
        //         $user = User::find($this->data['id']);

        // }

        return $this->data;
    }
}