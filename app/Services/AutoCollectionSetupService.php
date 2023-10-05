<?php
namespace App\Services;

use App\Models\Loan;

class AutoCollectionSetupService
{

    
    /**
     * Remita toggle status
     *
     * @param  mixed $loan
     * @return void
     */
    public function toggleAutoRemita(Loan $loan)
    {
        $value = !$loan->remita_active;
        
        $updates = ['remita_active' => $value];
        if ($loan->canBeSweptCard() && ($value == true)) {
            $updates['pause_sweep'] = false;
            $updates['auto_sweeping'] = false;
        }

        $loan->update($updates);

        return true;
    }

    public function toggleAutoSweep(Loan $loan)
    {
        $value = !$loan->auto_sweeping;
        $updates = ['auto_sweeping'=>$value, 'pause_sweep'=> false];
        
        if (isset($loan->mandateId) && ($value == true)) {
            $updates['remita_active'] = false;
        }
        $loan->update($updates);
        
        return true;
    }

    public function toggleManualSweep(Loan $loan)
    {
        $value = !$loan->pause_sweep;
        $updates = ['pause_sweep'=>$value, 'auto_sweeping'=> false];
        
        if (isset($loan->mandateId) && ($value == true)) {
            $updates['remita_active'] = false;
        }
        $loan->update($updates);
        
        return true;
    }
}