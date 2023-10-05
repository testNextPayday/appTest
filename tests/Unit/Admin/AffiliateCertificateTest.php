<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use App\Models\Settings;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PDF;

class AffiliateCertificateTest extends TestCase
{

    use RefreshDatabase;


    public function setUp(): void 
    {
        parent::setUp();

        Settings::create(
            [
                'name'=> 'Promissory Note Affiliates Rate', 
                'slug'=> 'promissiory_note_affiliate_rate', 
                'value'=> 1.5
            ]
        );
    }


    /**
     * Affiliate Receives Commission During Investment of promisory Note
     * @group Maintenance
     * @author Keania
     * @group badtest
     * @return void
     */
    public function testAffiliatesGetPaidWhenPromissoryNoteIsCreated()
    {
        PDF::shouldReceive('loadView')->andReturnSelf()
            ->getMock()
            ->shouldReceive('save')
            ->andReturn(true);
            
        //Event::fake();
        
       $affiliate = factory(\App\Models\Affiliate::class)->create();

       $oldWallet = $affiliate->wallet;
       
       $admin = factory(\App\Models\Admin::class)->create();

       $note = factory(\App\Models\InvestmentCertificate::class)->make()->toArray();
       $note['receiverType'] = strtolower($affiliate->toHumanReadable());
       $note['assignedPersonId'] = $affiliate->id;

       $route = route('admin.certificates.investment.store');

       $response = $this->actingAs($admin, 'admin')->post($route, $note);

       $rate = Settings::InvestorPromissoryNoteCommissionRate();

        $commission = ($rate * $note['tenure'] / 12)/100 * $note['amount'];

        $affiliate->refresh();

        $value = $commission + $oldWallet;

        $this->assertTrue(round($affiliate->wallet) == round($value));


    }
}
