<?php

namespace App\Models;

use App\Models\PenaltySetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employer extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];
    
    protected $dates = [
        'last_sweep'
    ];
       
   
    public function employees()
    {
        return $this->hasMany(Employment::class, 'employer_id');
    }

    public function penaltySetting()
    {
        return $this->morphOne(PenaltySetting::class, 'entity');
    }
    
    public function getRouteKeyName(){
        return 'id';
    }
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', 0);
    }

    public function scopeMerchant($query)
    {
        return $query->where('is_primary', 2);
    }
    
    public function employments()
    {
        return $this->hasMany(Employment::class);
    }
    
    public function bucket()
    {
        return $this->belongsTo(Bucket::class);
    }
    
    public function getEmployeeLoans()
    {
        $user_ids = $this->employments()->pluck('user_id')->toArray();
        
        return Loan::whereIn('user_id', $user_ids)->get();
    }
    public function employeeLoansQuery()
    {
        $user_ids = $this->employments()->pluck('user_id')->toArray();
        return Loan::whereIn('user_id', $user_ids);
    }
    public function getEmployeeUnfulfilledLoans()
    {
        $user_ids = $this->employments()->pluck('user_id')->toArray();
        
        return Loan::unfulfilled()->whereIn('user_id', $user_ids)->get();
    }


    public function mdaCollections()
    {
        if($this->id == 260){
            // rivers state central payroll
           return  Employer::where('name','like','%Rivers%')->where(['state'=>'Rivers','is_primary'=>1])->get(['id','name','state']);
        }

        if($this->id == 353){
            return  Employer::where('name','like','%Federal%')->orWhere('name','like','Nigeria%')->orWhere('name','like','National%')->where(['is_primary'=>1])->get(['id','name','state']);
        }

        return collect([]);
    }
    
    public function displayCollectionPlan($type = "primary")
    {
        $planGroups = config('settings.collection_methods');
        
        switch ($type) {
            case "secondary":
                $plan = $this->collection_plan_secondary;
                break;
            default:
                $plan = $this->collection_plan;
        }
        
        if (!$plan) return "Not Set";
        
        
        $group = substr($plan, 0, 1);
        
        switch($group) {
            case 0:
                // Temporary: For employers before the IPPIS Integration
                return "Remita (DDM)";
            case 1:
                return $planGroups['ddm'][$plan] . "(DDM)";
            case 2:
                return $planGroups['das'][$plan] . "(DAS)";
            case 3:
                return $planGroups['card'][$plan] . "(CARD)";
            default:
                return "No plan";
        }
    }
    
    // public function getCollectionPlanAttribute($plan) 
    // {
    //     return @config('unicredit.collection_plans')[$plan];
    // }
}
