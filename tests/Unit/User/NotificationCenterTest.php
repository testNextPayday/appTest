<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationCenterTest extends TestCase
{
    /**
     * Test we can view notifications
     *
     * @return void
     */
    public function testCanViewNotifications()
    {
        $user = factory(\App\Models\User::class)->create();

        $route = route('users.notification.index');
        
        $response = $this->actingAs($user)->get($route);

        $response->assertStatus(200);
    }


    
}
