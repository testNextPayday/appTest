<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManagesEmployerTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test **/
    public function an_authenticated_admin_can_create_an_employer()
    {
        $employer = make('App\Models\Employer')->toArray();
        
        $this->signIn(null, 'admin');
        
        $this->post("/ucnull/employers", $employer);
        
        $this->assertDatabaseHas('employers', [
            'name' => $employer['name'],
            'email' => $employer['email']
        ]);
    }
    
    /** @test **/
    public function an_authenticated_admin_can_update_an_employer()
    {
        $employer = create('App\Models\Employer');
        
        $this->signIn(null, 'admin');
        
        $update = ['name' => 'Haliburton', 'email' => 'employer@haliburton.com'];
        
        $this->post("/ucnull/employers/$employer->id", $update);
        
        $this->assertDatabaseHas('employers', [
            'name' => $update['name'],
            'email' => $update['email']
        ]);
    }
}
