<?php

namespace Tests\Feature\Investor;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Investor;
use App\Models\BankDetail;

class InvestorManagesProfileTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function an_authenticated_investor_can_view_profile()
    {
        
        $john = create(Investor::class, ['name' => 'John Doe']);
        
        $this->signIn($john, 'investor');
        
        $this->get('/investor/profile')
            ->assertSee('John Doe');
    }
    
    /** @test **/
    public function an_authenticated_investor_can_update_profile()
    {
        $john = create(Investor::class, ['name' => 'John Doe']);
        
        $this->signIn($john, 'investor');
        
        $update = [
            'name' => 'Jane Doe'
        ];
        
        $this->post('/investor/profile', $update);
        
        $this->get('/investor/profile')
            ->assertSee('Jane Doe')
            ->assertDontSee('John Doe');
    }
    
    /** @test **/
    public function an_authenticated_investor_can_update_his_bank_details()
    {
        $john = create(Investor::class);
        
        $johnsBank = create(BankDetail::class, ['owner_id' => $john->id, 'bank_name' => 'Ecobank']);
        
        $this->signIn($john, 'investor');
        
        $update = [
            'account_number' => '123456',
            'bank_code' => '023'
        ];
        
        
        $this->post('/investor/profile/bank', $update);
        
        $this->get('/investor/profile')
            ->assertSee('CITIBANK NIG LTD')
            ->assertDontSee('Ecobank')
            ->assertSee('123456');
    }
}
