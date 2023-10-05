<?php

namespace Tests\Unit\Affiliate;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateBorrowerTest extends TestCase
{

    use RefreshDatabase;


    public function setUp(): void 
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();
    }

   


    /**
     * A basic test that an affiliate can create a borrower
     * @group Maintenance
     * 
     * @author Keania
     * @return void
     */
    public function testAffiliateCanCreateBorrower()
    {
        Storage::fake();
        
        $affiliate = factory(\App\Models\Affiliate::class)->create();

        $affiliate->update(
            [
                'verification_applied'=>1, 
                'verified_at'=>now(), 
                'email_verified_at'=>now()
            ]
        );

        $data = $this->generateBorrowerData();

        $route = route('affiliates.borrowers.store');
        Notification::fake();
        $response = $this->actingAs($affiliate, 'affiliate')->post($route, $data);
         
        $response->assertStatus(200);

        $returnedData = json_decode($response->content());
        
        $user = $returnedData->user;

        $employmentId = $returnedData->employment_id;

        // assert the user was created
        $this->assertDatabaseHas('users', [
            'name'=> $data['surname']. ', '.$data['firstname']. ' '.$data['midname'],
            'bvn'=> $data['bvn'],
            'occupation'=> $data['occupation'],
            'dob'=> $data['dob']
            ]
        );

        // assert the employment was created
        $this->assertDatabaseHas('employments', 
        [
            'employer_id'=> $data['employer_id'],
            'department'=> $data['department'],
            'monthly_salary'=> $data['monthly_salary'],
            'mda'=> $data['mda'],
            'net_pay'=> $data['net_pay'],
            'gross_pay'=> $data['gross_pay'],
            'supervisor_phone'=> $data['supervisor_phone']
        ]
        );

        // create documents
        $documents = [
            'passport'=> UploadedFile::fake()->image('proof.jpg'),
            'confirmation'=> UploadedFile::fake()->image('proof.jpg'),
            'validId'=> UploadedFile::fake()->image('proof.jpg'),
            'appointment'=> UploadedFile::fake()->image('proof.jpg'),
            'workId'=> UploadedFile::fake()->image('proof.jpg'),
            'employment_id'=> $employmentId
        ];

        $docUrl = route('affiliates.documents.borrowers');

        $response = $this->actingAs($affiliate, 'affiliate')->post($docUrl, $documents);

        //$user = User::whereEmail($data['email'])->first();
       
        $response->assertStatus(200);

    }


     
    /**
     * Generate the information affiliares uses to create borrowers
     *
     * @return array
     */
    protected function generateBorrowerData() : array 
    {

        return [
            'phone' => '08185695490',
            'email' => $this->faker->safeEmail,
            'firstname' => $this->faker->firstName,
            'midname' => $this->faker->firstName,
            'surname' => $this->faker->firstName,
            'dob' =>  '1996-07-08',
            'gender' => $this->faker->randomElement([1,2]),
            'marital_status' => $this->faker->randomElement([1,2]),
            'no_of_children' => $this->faker->numberBetween($min = 0, $max = 5),
            'occupation' => $this->faker->jobTitle,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'lga' => $this->faker->city,
            'state' => $this->faker->state,
            'next_of_kin' => $this->faker->name,
            'next_of_kin_phone' => '08185695490',
            'next_of_kin_address' => $this->faker->streetAddress,
            'relationship_with_next_of_kin' => $this->faker->randomElement(['Son', 'Daughter', 'Cousin']),
            'employer_id' =>  factory(\App\Models\Employer::class)->create()->id,
            'position' => $this->faker->jobTitle,
            'department' => $this->faker->text,
            'date_employed' =>  '2018-07-08',
            'date_confirmed' => '2020-07-08',
            'monthly_salary' => $this->faker->numberBetween($max = 100000, $min = 500000),
            //'monthly_salary' => 'required',
            'gross_pay' => $this->faker->numberBetween($max = 1000000, $min=5000),
            'net_pay' => $this->faker->numberBetween($max = 1000000, $min=5000),
            'supervisor_name' => $this->faker->name,
            'supervisor_email' => $this->faker->safeEmail,
            'supervisor_phone' => '08185695490',
            'supervisor_grade' => $this->faker->randomElement(['1','2','3','4','5','6']),
            'bankCode' => '070',
            'accountNumber' => 5678987654,
            'bankName'=> 'Fidelity Bank',
            'bvn' => '23456768970',
            'mda'=> $this->faker->text,
            'payroll_id'=> '546576879',
            'user_type'=> 0
           
        ];
    }
}
