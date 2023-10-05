<?php

namespace Tests\Feature\Staff;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TicketRepliedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @group Maintenance
     * @author Keania
     * 
     * @return void
     */
    public function testStaffCanRespondToTicket()
    {
        //Notification::fake();

        $staff = factory(\App\Models\Staff::class)->create(
            ['roles'=> 'manage_support']
        );

        $ticket = factory(\App\Models\Ticket::class)->create();

        $route = route('staff.ticket.respond', ['ticket'=> $ticket->reference]);

        $data = [
            'message'=> \Faker\Factory::create()->sentence
        ];

        $this->signIn($staff, 'staff');

        $response = $this->post($route, $data);

        $response->assertSessionHas(['success'=> 'Reply successfully sent']);

        $this->assertTrue($staff->ticketReplies->count() > 0);

        $user = $ticket->owner;

        //Notification::assertSentTo( $user, TicketRepliedNotification::class);
    }
}
