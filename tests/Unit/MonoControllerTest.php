<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\MonoStatement\BaseMonoStatementService;

class MonoControllerTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Checks a users mono status fails when there is no mono id
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testWeCheckUserMonoStatusFails()
    {
        // Given a user
        $user = factory(\App\Models\User::class)->create();

        // Given a bank details
        $bankDetail = factory(\App\Models\BankDetail::class)->create([
            'owner_id'=> $user->id,
            'owner_type'=> get_class($user)
        ]);

        $route = route('mono.check.status');

        $response = $this->actingAs($user)->get($route);

        $response->assertStatus(200);

        $response->assertJsonFragment(['status'=>false, 'message'=>'User Authentication Failed']);
    }
    
    /**
     * When there exists a mono id this works
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testWeCheckUserMonoStatusSucceeds()
    {
        // Given a user
        $user = factory(\App\Models\User::class)->create();

        // Given a bank details
        $bankDetail = factory(\App\Models\BankDetail::class)->create([
            'owner_id'=> $user->id,
            'owner_type'=> get_class($user),
            'mono_id'=> base64_encode('get the hell out')
        ]);

        $route = route('mono.check.status');

        $response = $this->actingAs($user)->get($route);

        $response->assertStatus(200);

        $response->assertJsonFragment(['status'=>true, 'message'=>'User Successfully Authenticated']);
    }
    
    /**
     * Test we send an authorization code and we get back a mono id
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testMonoAuthWorks()
    {
        $fakeResponse = ['id'=>"5f171a530295e231abca1153"];

        $payload = ['code'=> base64_encode("This is mono code")];

        $user = factory(\App\Models\User::class)->create();

        // Given a bank details
        $bankDetail = factory(\App\Models\BankDetail::class)->create([
            'owner_id'=> $user->id,
            'owner_type'=> get_class($user)
        ]);

        $monoService = Mockery::mock(BaseMonoStatementService::class);

        $monoService->shouldReceive('authRequest')->andReturn(true);

        $monoService->shouldReceive('getResponse')->andReturn($fakeResponse,$this->getFakeInstResponse($bankDetail));

        $monoService->shouldReceive('checkBankInformation')->andReturn(true);

        $this->app->instance(BaseMonoStatementService::class, $monoService);

        $route = route('mono.auth');

        $response = $this->actingAs($user)->post($route, $payload);
       
        $response->assertStatus(200);

        $response->assertJsonFragment(['status'=>true, 'message'=>'User Successfully Authenticated']);
    }

    
    /**
     * Get fake institution response
     *
     * @param  mixed $bankDetail
     * @return void
     */
    public function getFakeInstResponse($bankDetail)
    {
        return [
            "meta"=> [
                "data_status"=>"AVAILABLE"
              // Available, Processing, Failed
            ],
            "account"=> [
                "_id"=> "5feec8ce95e8dc6a52e53257",
                "institution"=> [
                    "name"=> $bankDetail->bank_name,
                    "bankCode"=> $bankDetail->bank_code,
                    "type"=> "PERSONAL_BANKING"
                  // or BUSINESS_BANKING
                ],
                "name"=> $bankDetail->owner->name,
                "accountNumber"=> $bankDetail->account_numbers,
                "type"=> "SAVINGS ACCOUNT",
                "balance"=> 538786,
                "currency"=> "NGN",
                "bvn"=> "1595"
            ]
        ];
    }

    
    /**
     * Test given an id we can retrieve statement
     *
     * @return void
     */
    public function testWeCanRetrieveBankStatement()
    {
        // An account id
        $payload  = ['id'=>base64_encode('payload data')];

        $monoService = Mockery::mock(BaseMonoStatementService::class);

        $monoService->shouldReceive('statementRequest')->andReturn(true);

        $monoService->shouldReceive('getResponse')->andReturn($this->getFakeStatementResponse());

        $this->app->instance(BaseMonoStatementService::class, $monoService);

        $route = route('mono.statement');

        $response = $this->get($route, $payload);

        $response->assertStatus(200);

        $response->assertJsonFragment(['status'=>true, 'message'=>'Bank Statement Retrieved Successfully']);


    }


    public function getFakeStatementResponse()
    {
        return [
            "id"=>"iUHoy3NxM535EpBaK7Ib",
            "status"=> "BUILT",
            "path"=> "https://api.withmono.com/statements/RePFYwV9AOnPqDCsk.pdf"
        ];
    }
}
