<?php

namespace App\Observers;

use App\Models\Affiliate;

use App\Traits\ReferenceNumberGenerator;

class AffiliateObserver
{
    use ReferenceNumberGenerator;
    
    /**
     * Handle the affiliate "creating" event.
     *
     * @param  \App\Models\Affiliate  $affiliate
     * @return void
     */
    public function creating(Affiliate $affiliate)
    {
        $affiliate->password = bcrypt($affiliate->password);
        $affiliate->avatar = 'public/defaults/avatars/default.png';
        $affiliate->reference = $this->generateReference(Affiliate::class, 'NPA-');
        return $affiliate;
    }
}
