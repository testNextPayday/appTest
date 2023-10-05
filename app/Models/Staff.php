<?php

namespace App\Models;

use Storage;
use Keygen\Keygen;
use App\Traits\Payable;
use App\Models\LoanNote;
use App\Models\BankDetail;
use App\Models\TicketMessage;

use App\Traits\ReadableModel;
use App\Traits\HasGatewayRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\Staff\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    use SoftDeletes, Notifiable,Payable,HasGatewayRecords, ReadableModel;
    
    protected $guarded = [];
     
    public function getRouteKeyName()
    {
        return 'reference';    
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }



    public function scopePayable($query)
    {
        return $query->where('is_active',1)->whereHas('banks');
    }
    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function banks()
    {
        return $this->morphMany(BankDetail::class, 'owner');
    }
    
    public function usersAdded()
    {
        return $this->morphMany(User::class, 'adder');
    }
    
    public function users()
    {
        return $this->hasMany(User::class, 'staff_id');
    }

    /**
     * Staff loan notes
     *
     * @return void
     */
    public function notes()
    {
        return $this->morphMany(LoanNote::class, 'owner');
    }
    
    public function investorsAdded()
    {
        return $this->morphMany(Investor::class, 'adder');
    }
    
    public function investors()
    {
        return $this->hasMany(Investor::class, 'staff_id');
    }
    
    public function bids()
    {
        return $this->hasMany(Bid::class);    
    }
    
    public function loanRequests()
    {
        return $this->morphMany(LoanRequest::class, 'placer');    
    }
    
    public function funds()
    {
        return $this->hasMany(LoanFund::class);
    }
    
    public function loans()
    {
        return $this->morphMany(Loan::class, 'collector');
    }


    public function ticketReplies()
    {
        return $this->morphMany(TicketMessage::class, 'owner');
    }
    
    
    /**
     * Returns a affiliates a staff is supervising
     * 
     */
    public function affiliates()
    {
        return $this->morphMany(Affiliate::class, 'supervisor');    
    }
    
    public function getAvatarAttribute($avatar)
    {
        return asset(Storage::url($avatar));
    }
    
    public function getNameAttribute()
    {
        return "$this->lastname, $this->firstname $this->midname";
    }
    
    public function getRoleListAttribute()
    {
        return array_map(function($role) { 
            return trim($role);
        }, explode(",", $this->roles));
    }
    
    public function manages($entity)
    {
        return in_array("manage_$entity", $this->roleList);    
    }
    
    private function generateKey()
    {
        // prefixes the key with a random integer between 1 - 9 (inclusive)
        return Keygen::numeric(9)->prefix(mt_rand(1, 9))->prefix('NPS-')->generate(true);
    }

    public function generateReference()
    {
        $reference = $this->generateKey();
        
        // Ensure ID does not exist
        // Generate new one if ID already exists
        while (Staff::whereReference($reference)->count() > 0) {
            $reference = $this->generateKey();
        }

        return $reference;
    }
}
