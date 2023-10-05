<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiSignInTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        \Artisan::call('passport:install');
    }
    /**
     * @group Maintenance
     *  @author Keania
     *
     * @return void
     */
    public function testSignInFailsWithoutPhone()
    {
        $credentials = [
            'phone'=>null,
            'password'=>'kabolobari'
        ];
        $response = $this->json('POST',route('api.login'),$credentials);
        $response->assertStatus(422);
        $response->assertJsonFragment(["phone"=> [
            "The phone field is required."
        ]]);
    }

     /**
     * @group Maintenance
     *  @author Keania
     *
     * @return void
     */
    public function testSignInFailsWithoutPassword()
    {
        $credentials = [
            'phone'=>'08185695490',
            'password'=>null
        ];
        $response = $this->json('POST',route('api.login'),$credentials);
        $response->assertStatus(422);
        $response->assertJsonFragment(["password"=> [
            "The password field is required."
        ]]);
    }


    /**
     * @group Maintenance
     *  @author Keania
     *
     * @return void
     */
    public function testSignInSuccedWithPhone()
    {
         $credentials = [
            'phone'=>'08185695490',
            'password'=>'kabolobari'
        ];

        $user = factory(\App\Models\User::class)->create([
            'phone'=>$credentials['phone'],
            'password'=>bcrypt($credentials['password'])
        ]);
       
        $response = $this->json('POST',route('api.login'),$credentials);
    
        $response->assertStatus(200);
        $response->assertJsonFragment(["token_type"=> 'Bearer']);
    }
}
