<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffUser extends Model
{
    protected $table = 'staff_user';
    
    protected $guarded = [];
    
    public function assignmentExist($staff_id,$user_id)
    {
        $occur = StaffUser::where('user_id', $user_id)->where('staff_id', $staff_id)->where('status', true)->first();
        
        if($occur){
            return $occur;
        } return false;
    }
}
