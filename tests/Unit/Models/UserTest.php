<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = create(User::class);
    }
    
    /** @test **/
    public function a_user_can_generate_reference()
    {
        $this->assertNotNull($this->user->reference);
    }
}
