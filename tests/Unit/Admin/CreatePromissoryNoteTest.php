<?php

namespace Tests\Unit\Admin;

use Carbon\Carbon;
use Tests\TestCase;
use App\Mail\InvestmentMade;
use App\Models\PromissoryNote;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use App\Events\PromissoryNoteCreatedEvent;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PDF;
class CreatePromissoryNoteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group Maintenance
     * @author Keania
     * @group badtest
     * We create promissory Note for Investor
     *
     * @return void
     */
    public function testPromissoryNoteCreationWorksBasically()
    {
        list($investor, $data, $admin, $route)  = $this->generateTestData();

        $response = $this->actingAs($admin, 'admin')->post($route, $data);
        
        $response->assertSessionHas('success', 'Promissory Note is pending approval');

        // A promisory note gets created for investor
        $assertionData = $this->getPRNoteDatabaseAssertion($data);
        unset($assertionData['start_date']);
        unset($assertionData['reference']);
        $this->assertDatabaseHas('promissory_notes', $assertionData);
        // A certificate is being created for the promissory note

       // $this->assertDatabaseHas('investment_certificates', $this->getCertDatabaseAssertion($data));

    }

    
    /**
     * @group Maintenance
     * 
     * @author Keania 
     * @group badtest
     * We can approve promissory notes and get certificates
     *
     * @return void
     */
    public function testPromissoryNoteApprovalWorksBasically()
    {
        Mail::fake();

        PDF::shouldReceive('loadView')->andReturnSelf()
            ->getMock()
            ->shouldReceive('save')
            ->andReturn(true);
        
        //Event::fake();
        // preparing the variables
        $admin = factory(\App\Models\Admin::class)->create();
        $promissoryNote = factory(\App\Models\PromissoryNote::class)->create(['status'=>0]);
        $route = route('admin.promissory-notes.approve', ['promissory_note'=> $promissoryNote->reference]);

        // We hit the admin approval route
        $response = $this->actingAs($admin, 'admin')->post($route);
        
        // We check that the promissory note is now active
        $promissoryNote->refresh();
       
        $this->assertTrue(intval($promissoryNote->status) == 1);

        // We check that a certificate has been created for the promissory note
        $this->assertTrue($promissoryNote->certificate->exists());

        Mail::assertNotSent(InvestmentMade::class);

        //Event::assertNotDispatched(PromissoryNoteCreatedEvent::class);

    
    }

    
    /**
     * @group Maintenance
     * @author Keania
     *  @group badtest
     *  We can update a pending loan
     * 
     * @return void
     */
    public function testPromissoryNoteUpdateWorks()
    {
        $admin = factory(\App\Models\Admin::class)->create();

        $promissoryNote = factory(\App\Models\PromissoryNote::class)->create(['status'=>0]);

        $data = $promissoryNote->toArray();

        $data['rate'] = 5;

        $route = route('admin.promissory-notes.update', ['promissory_note'=> $promissoryNote->reference]);

        // We hit the admin update route
        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        $promissoryNote->refresh();
       
        $this->assertTrue(intval($promissoryNote->rate) == 5);

    }

    
    /**
     * Cannot update an active note
     *  @group badtest
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testPromissoryNoteCannotUpdateActive()
    {
        $admin = factory(\App\Models\Admin::class)->create();

        $promissoryNote = factory(\App\Models\PromissoryNote::class)->create(['status'=>1, 'rate'=> 2]);
       
        $data = $promissoryNote->toArray();

        $data['rate'] = 5;

        $route = route('admin.promissory-notes.update', ['promissory_note'=> $promissoryNote->reference]);

        // We hit the admin update route
        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        $response->assertSessionHas(['failure'=>'We cannot update a note that is no longer pending']);
        $promissoryNote->refresh();
        
        $this->assertFalse(intval($promissoryNote->rate) == 5);
    }


    /**
     * @group Maintenance
     * @author Keania
     *  @group badtest
     *  We can delete a promissory note
     * 
     * @return void
     */
    public function testPromissoryNoteDeletionWorks()
    {
        $admin = factory(\App\Models\Admin::class)->create();

        $promissoryNote = factory(\App\Models\PromissoryNote::class)->create(['status'=>0]);

        $route = route('admin.promissory-notes.delete', ['promissory_note'=> $promissoryNote->reference]);

        // We hit the admin update route
        $response = $this->actingAs($admin, 'admin')->post($route);

        $this->assertTrue(PromissoryNote::where('reference', $promissoryNote->reference)->first() == null);

    }

     /**
     * @group Maintenance
     * @author Keania
     *  @group badtest
     *  We can delete a promissory note
     * 
     * @return void
     */
    public function testPromissoryNoteCannotDeleteActive()
    {
        $admin = factory(\App\Models\Admin::class)->create();

        $promissoryNote = factory(\App\Models\PromissoryNote::class)->create(['status'=>1]);

        $route = route('admin.promissory-notes.delete', ['promissory_note'=> $promissoryNote->reference]);

        // We hit the admin update route
        $response = $this->actingAs($admin, 'admin')->post($route);

        $response->assertSessionHas(['failure'=> 'We cannot delete a note that is no longer pending']);


        $this->assertTrue(PromissoryNote::where('reference', $promissoryNote->reference)->first() != null);

    }

    
    /**
     * Generate Test Data
     *
     * @return void
     */
    protected function generateTestData()
    {
        // With a selected investor rates an the other parameters 
        $investor = factory(\App\Models\Investor::class)->create(['role'=> 2]);

        $data = $this->getPromissoryData();
        $data['investor_id'] = $investor->id;
        $data['amount'] = $data['principal'];
        unset($data['principal']);

        $admin  = factory(\App\Models\Admin::class)->create();

        $route = route('admin.promissory-notes.store');

        return [$investor, $data, $admin, $route];
    }


    
    /**
     * Promissory Note Data
     * 
     * @return void
     */
    protected function getPromissoryData()
    {
        $note = factory(\App\Models\PromissoryNote::class)->make()->toArray();
        $setting  = factory(\App\Models\PromissoryNoteSetting::class)->create();
        
        $note['setting_id'] = $setting->id;
        $note['tax'] = $setting->tax_rate;
        $note['rate'] = $setting->interest_rate;

        return $note;
    }
    
    /**
     * Data that should be in the databse
     *
     * @param  mixed $data
     * @return void
     */
    protected function getPRNoteDatabaseAssertion($data)
    {

        $data['principal'] = $data['amount'];
        $data = $this->unsetArrayByKeys(['amount', 'setting_id'], $data);
        $amount  = $data['principal'];
        $rate = $data['rate'];
        $tenure = $data['tenure'];
        $interestEarned = (($amount * ($rate/100)) / 12) * $tenure;
        $tax = ($data['tax']/ 100) * $interestEarned;
        $data['interest'] = $interestEarned - $tax;
        $data['start_date'] = $this->getStartDate($data);

        $data['maturity_date'] = (string)Carbon::parse($data['start_date'])->addMonths($data['tenure']);
        return $data;
    }

     /**
     * Checks the start date of the loan to ensure it works
     *
     * @param  mixed $request
     * @return void
     */
    public function getStartDate($data)
    {

        $startDate = Carbon::parse($data['start_date']);

        if ($startDate->day > 28) {

            $startDate->day = 1;

            $startDate->addMonth();

            $date = $startDate->toDateString();

            return $date;
        }

        return $startDate->toDateString();


    }

    
    /**
     * Data that should be in the databse
     *
     * @param  mixed $data
     * @return void
     */
    protected function getCertDatabaseAssertion($data)
    {

        $data = $this->unsetArrayByKeys(['investor_id', 'tenure', 'send_email'], $data);
        $data['start_date'] = Carbon::parse($data['start_date'])->toDateString();
        return $data;
    }
    
    /**
     * Unset array by keys
     *
     * @param  array $keys
     * @param  array $array
     * @return void
     */
    private function unsetArrayByKeys($keys, $array)
    {
        $data = $array;
        foreach($keys as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
