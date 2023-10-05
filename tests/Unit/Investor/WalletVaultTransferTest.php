<?php

namespace Tests\Unit\Investor;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WalletVaultTransferTest extends TestCase
{
    /**
     * @group Maintenance
     *  @author Keania
     *
     * @return void
     */
    public function testInvestorCanTransferWalletToVault()
    {
       $investor = factory(\App\Models\Investor::class)->create([
           'wallet'=>300000,
           'vault'=>0.0
       ]);

       $data = [
           'amount'=>50000,
           'flow'=>'WTV',
          
       ];

       $response = $this->actingAs($investor,'investor')->post(route('investor.transfer.wallet-vault',['sender_id'=>$investor->id]),$data);

       //assert that the json return has vault + $data['amount'] asses
       $vaultBalance = $investor->vault + $data['amount'];
       $walletBalance = $investor->wallet  - $data['amount'];
       $jsonFragment = [
           'wallet'=>$walletBalance,
           'vault'=>$vaultBalance
       ];

       $response->assertJsonFragment($jsonFragment);
       $response->assertStatus(200);
       $this->assertDatabaseHas('investors',$jsonFragment);
    }


    /**
     * testInvestorCanTransferVaultToWallet
     *
     * @group Maintenance
     * @author  Keania
     */
    public function testInvestorCanTransferVaultToWallet()
    {
        $investor = factory(\App\Models\Investor::class)->create([
            'wallet'=>0.0,
            'vault'=>300000
        ]);
 
        $data = [
            'amount'=>50000,
            'flow'=>'VTW',
           
        ];
 
        $response = $this->actingAs($investor,'investor')->post(route('investor.transfer.wallet-vault',['sender_id'=>$investor->id]),$data);
 
        //assert that the json return has vault + $data['amount'] asses
        $vaultBalance = $investor->vault - $data['amount'];
        $walletBalance = $investor->wallet  + $data['amount'];
        $jsonFragment = [
            'wallet'=>$walletBalance,
            'vault'=>$vaultBalance
        ];
 
        $response->assertJsonFragment($jsonFragment);
        $response->assertStatus(200);
        $this->assertDatabaseHas('investors',$jsonFragment);
    }


    /**
     * testInvestorCannotTransferNullAmount
     *
     * @group Maintenance
     * @author Keania
     */
    public function testInvestorCannotTransferNullAmount()
    {
        $investor = factory(\App\Models\Investor::class)->create([
            'wallet'=>0.0,
            'vault'=>300000
        ]);
 
        $data = [
            'amount'=>null,
            'flow'=>'VTW',
           
        ];
 
        $response = $this->actingAs($investor,'investor')->post(route('investor.transfer.wallet-vault',['sender_id'=>$investor->id]),$data);
 
    
 
        $response->assertStatus(400);
      //$response->assertSessionHasErrors(['amount']);
    }

     /**
     * testInvestorCannotTransferToNullflow
     *
     * @group Maintenance
     * @author Keania
     */
    public function testInvestorCannotTransferToNullflow()
    {
        $investor = factory(\App\Models\Investor::class)->create([
            'wallet'=>0.0,
            'vault'=>300000
        ]);
 
        $data = [
            'amount'=>50000,
            'flow'=>null,
           
        ];
 
        $response = $this->actingAs($investor,'investor')->post(route('investor.transfer.wallet-vault',['sender_id'=>$investor->id]),$data);
 
        //assert that the json return has vault + $data['amount'] asses
        $vaultBalance = $investor->vault - $data['amount'];
        $walletBalance = $investor->wallet  + $data['amount'];
       
        $response->assertStatus(400);
       //$response->assertSessionHasErrors(['flow']);
    }


     /**
     * testInvestorCannotTransferMoreThanInVault
     *
     * @group Maintenance
     * @author Keania
     */
    public function testInvestorCannotTransferMoreThanInVault()
    {
        $investor = factory(\App\Models\Investor::class)->create([
            'wallet'=>0.0,
            'vault'=>300000
        ]);
 
        $data = [
            'amount'=>500000,
            'flow'=>'VTW',
           
        ];
 
        $response = $this->actingAs($investor,'investor')->post(route('investor.transfer.wallet-vault',['sender_id'=>$investor->id]),$data);
        $response->assertStatus(400);
       $response->assertJson(['failure'=>'Invalid transfer amount']);
    }

     /**
     * testInvestorCannotTransferMoreThanInWallet
     *
     * @group Maintenance
     * @author Keania
     */
    public function testInvestorCannotTransferMoreThanInWallet()
    {
        $investor = factory(\App\Models\Investor::class)->create([
            'wallet'=>300000,
            'vault'=>0.0
        ]);
 
        $data = [
            'amount'=>500000,
            'flow'=>'WTV'
        ];
 
        $response = $this->actingAs($investor,'investor')->post(route('investor.transfer.wallet-vault',['sender_id'=>$investor->id]),$data);
 
        $response->assertJson(['failure'=>'Invalid transfer amount']);
    }


    


    


    

    
}
