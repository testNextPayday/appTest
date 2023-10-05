<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Facades\BulkSms;
use App\Models\Employer;
use App\Models\Settings;
use App\Models\PhoneToken;
use App\Events\SendOTPEvent;
use Illuminate\Support\Facades\Event;
use App\Http\Resources\EmployerAPIResource;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiSignUpTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }
    /**
     * A basic unit test example.
     *
     * @group Maintenance
     * @author Keania
     */
    public function testCanRetrieveEmployers()
    {
        $employers = factory(\App\Models\Employer::class,10)->create([
            'is_primary'=>0
        ]);
        $response = $this->json('GET',route('api.fetch.employers'));
        $response->assertStatus(200);
       
    }


     /**
     * A basic unit test example.
     *
     * @group Maintenance
     * @author Keania
     */
    public function testSendTokenFailsWithoutPhone()
    {
        $credentials = [
            'phone'=>null
        ];

        $response = $this->json('POST',route('api.sendToken'));
        $response->assertStatus(422);
        $response->assertJsonFragment(["phone"=> [
            "The phone field is required."
        ]]);
    }


     /**
     * A basic unit test example.
     *
     * @group Maintenance-OTP
     * @author Keania
     */
    public function testSendTokenSucceedPhone()
    {
        //Event::fake();

        $credentials = [
            'phone'=>'08185695490'
        ];

        $response = $this->json('POST', route('api.sendToken'), $credentials);

        // Event::assertDispatched(SendOTPEvent::class,function($e) use($credentials){
        //     return $e->data['phone'] == $credentials['phone'];
        // });
    
        $response->assertStatus(200);
        $response->assertExactJson(['status'=>true,'message'=>' An activation token has been sent to '.$credentials['phone']]);
    }

     /**
     * A basic unit test example.
     *
     * @group Maintenance
     * @author Keania
     */
    public function testVerifyTokenFails()
    {
       $tokenModel = factory(\App\Models\PhoneToken::class)->create();
        $credentials = [
            'phone'=>$tokenModel->phone,
            'token'=>'NPD-123456890'
        ];
        $response = $this->json('POST',route('api.verify.token'),$credentials);
    
        $response->assertStatus(422);
        $response->assertExactJson(['status'=>false,'message'=>'Token mismatch']);
    }


    /**
     * A basic unit test example.
     *
     * @group Maintenance
     * @author Keania
     */
    public function testVerifyTokenSucceed()
    {
       $tokenModel = factory(\App\Models\PhoneToken::class)->create();
        $credentials = [
            'phone'=>$tokenModel->phone,
            'token'=>$tokenModel->token
        ];
        $response = $this->json('POST',route('api.verify.token'),$credentials);
    
        $response->assertStatus(200);
        $response->assertExactJson(['status'=>true,'message'=>'Token was verified successfully']);
        $this->assertDatabaseHas('phone_tokens',[
            'phone'=>$tokenModel->phone,
            'token'=>null,
            'verified'=>1
        ]);
    }


    /**
     * A basic unit test example.
     *
     * @group Maintenance
     * @author Keania
     */
    public function testRegistrationSucceeds()
    {
        $data = $this->getRegistrationData();

        $response = $this->json('POST',route('api.register'),$data);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('users',[
            'phone'=>$data['phone'],
            'email'=>$data['email']
        ]);
        $this->assertDatabaseHas('employments',[
            'employer_id'=>$data['employer_id'],
            'monthly_salary'=>$data['monthly_salary']
        ]);
    }

    
    /**
     * Registration for salary now users work
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testSalaryNowRegistrationSucceeds()
    {
        // Create salary now employer
        $employer = factory(\App\Models\Employer::class)->create();

        // Set the user as the salary now employer
        Settings::create([
            'slug'=>'salary_now_employer',
            'name'=> 'Salary Now Employer',
            'value'=> $employer->id
        ]);
        $data = $this->getRegistrationDataSalaryNow();

        $workExtras =  [
            'business_name'=>$data['business_name'], 
            'business_address'=>$data['business_address'],
            'business_desc'=> $data['business_desc']
        ];

        $response = $this->json('POST',route('api.register.salary-now'),$data);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('users',[
            'phone'=>$data['phone'],
            'email'=>$data['email']
        ]);
        $this->assertDatabaseHas('employments',[
            'employer_id'=>$employer->id,
            'monthly_salary'=>$data['monthly_salary'],
            'work_extras'=> json_encode($workExtras)
        ]);
    }

     /**
     * A basic unit test example.
     *
     * @group Maintenance
     * @author Keania
     */
    public function testRegistrationFails()
    {
        $data = $this->getRegistrationData();
        unset($data['phone']);
        unset($data['email']);
        $response = $this->json('POST',route('api.register'),$data);
        
        $response->assertStatus(422);
        $response->assertJsonFragment(["phone"=> [
            "The phone field is required."
        ],"email"=>["The email field is required."]]);
    }


    protected function getRegistrationDataSalaryNow()
    {
        $registerData = $this->getRegistrationData();
        // set employer_id to null
        $registerData['employer_id'] = null;

        // add business names and what have you
        $registerData['business_name'] = 'Abu Adudu & Sons Ltd';
        $registerData['business_desc'] = 'This is what i call best business';
        $registerData['business_address'] = 'No 12 Ikemefuna street';

        return $registerData;
    }

    protected function getRegistrationData()
    {
        $faker = \Faker\Factory::create();
        return [
            'firstname'=>$faker->firstName,
            'lastname'=>$faker->lastName,
            'phone'=>'08185695490',
            'email'=>$faker->safeEmail,
            'password'=>'kabolobari',
            'password_confirmation'=>'kabolobari',
            'gender'=>$faker->randomElement([0,1]),
            'employer_id'=>$faker->randomElement(range(1,6)),
            'monthly_salary'=>$faker->numberBetween($min=10000,$max=50000)
        ];
    }

    
    /**
     * Test that an affiliate can refund 
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testAffiliateCodeWorksAsExpected()
    {
        $affiliate = factory(\App\Models\Affiliate::class)->create();

        $refcode = $affiliate->reference;

        $data = $this->getRegistrationData();

        $data['ref_code'] = $refcode;

        $response = $this->json('POST',route('api.register'),$data);
        
        $response->assertStatus(200);

        $this->assertDatabaseHas('users',[
            'phone'=>$data['phone'],
            'email'=>$data['email'],
            'adder_id'=>$affiliate->id,
            'adder_type'=> get_class($affiliate)
        ]);
        
        $this->assertDatabaseHas('employments',[
            'employer_id'=>$data['employer_id'],
            'monthly_salary'=>$data['monthly_salary']
        ]);
    }

    
    /**
     * Test that a user can refer with reference
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testUserCodeWorksAsExpected()
    {
        $user = factory(\App\Models\User::class)->create();

        $refcode = $user->reference;

        $data = $this->getRegistrationData();

        $data['ref_code'] = $refcode;

        $response = $this->json('POST',route('api.register'), $data);
        
        $response->assertStatus(200);

        $this->assertDatabaseHas('users',[
            'phone'=>$data['phone'],
            'email'=>$data['email'],
            'adder_id'=>$user->id,
            'adder_type'=> get_class($user)
        ]);
        
        $this->assertDatabaseHas('employments',[
            'employer_id'=>$data['employer_id'],
            'monthly_salary'=>$data['monthly_salary']
        ]);


    }
    
    /**
     * Test that referral code works with phone
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testUserCodeWorksAsExpectedPhone()
    {
        $faker = \Faker\Factory::create();
        $user = factory(\App\Models\User::class)->create(['phone'=>'08185695492']);

        $refcode = $user->phone;

        $data = $this->getRegistrationData();

        $data['ref_code'] = $refcode;

        $response = $this->json('POST', route('api.register'), $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users',[
            'phone'=>$data['phone'],
            'email'=>$data['email'],
            'adder_id'=>$user->id,
            'adder_type'=> get_class($user)
        ]);
        
        $this->assertDatabaseHas('employments',[
            'employer_id'=>$data['employer_id'],
            'monthly_salary'=>$data['monthly_salary']
        ]);


    }


}
