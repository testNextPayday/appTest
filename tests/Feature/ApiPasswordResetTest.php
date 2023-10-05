<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a user with invalid phone is does not get otp
     *
     * @return void
     */
    public function testWrongPhoneGetsOut()
    {
        $data = ['phone'=>'08185695490'];

        $route = route('api.reset-password.code');

        $response = $this->post($route, $data);

        $response->assertStatus(422);

        // $response->assertExactJson(
        //     ['status'=>false, 'message'=>'This phone has no account linked to it']
        // );
    }

    
    /**
     * Test a real user goes through
     *
     * @return void
     */
    public function testRightPhoneGetsOTP()
    {
        $user = factory(\App\Models\User::class)->create(
            ['phone'=>'08185695490']
        );

        $data = ['phone'=> $user->phone];

        $route = route('api.reset-password.code');

        $response = $this->post($route, $data);

        $response->assertStatus(200);

        // $response->assertExactJson(
        //     ['status'=>true, 'message'=>' Your password reset token is '+ $user->phone, 'data'=>['phone'=>$data['phone']]]
        // );

        $this->assertDatabaseHas('phone_tokens', ['phone'=>$data['phone']]);
    }
    
    /**
     * Test with valid token every thing works
     *
     * @return void
     */
    public function testTokenConfirmationWorks()
    {
        $phoneModel = factory(\App\Models\PhoneToken::class)->create();

        $data = [
            'phone'=>$phoneModel->phone,
            'token'=> $phoneModel->token
        ];

        $route = route('api.reset-password.confirm');
        $response = $this->post($route, $data);

        $response->assertStatus(200);

        //$response->assetExactJson(['status'=>true, 'message'=>'Token was correct', 'data'=>['phone'=>$data['phone']]]);
    }


    /**
     * Test with invalid token no thing works
     *
     * @return void
     */
    public function testTokenConfirmationFails()
    {
        $faker = \Faker\Factory::create();

        $data = [
            'phone'=>$faker->phoneNumber,
            'token'=> $faker->numberBetween($min = 3000, $max = 20000)
        ];

        $route = route('api.reset-password.confirm');
        $response = $this->post($route, $data);

        $response->assertStatus(422);

        //$response->assetExactJson(['status'=>false, 'message'=>'Token Mismatch']);
    }

    
    /**
     * Test that password creation works
     *
     * @return void
     */
    public function testCreatePasswordWorks()
    {
        $user = factory(\App\Models\User::class)->create(['phone'=>'08185695490']);

        $data = [
            'password'=>'keania',
            'password_confirmation'=>'keania',
            'phone'=> $user->phone
        ];

        $route = route('api.create.password');

        $response = $this->post($route, $data);

       
        $response->assertStatus(200);

        //$response->assertExactJson(['status'=>true, 'message'=> 'Password Reset was successful']);

        $user->refresh();

        $this->assertTrue(Hash::check($data['password'], $user->password));

    }


     /**
     * Test that password creation fails on confirm
     *
     * @return void
     */
    public function testCreatePasswordFailsOnConfirm()
    {
        $user = factory(\App\Models\User::class)->create();

        $data = [
            'password'=>'keania',
            'password_confirmation'=>'james',
            'phone'=> $user->phone
        ];

        $route = route('api.create.password');

        $response = $this->post($route, $data);

        $response->assertStatus(422);

    }
}
