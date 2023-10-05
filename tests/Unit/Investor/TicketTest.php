<?php

namespace Tests\Unit\Investor;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

     /**
     * Test a user can successfully create tickets
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testUserCanCreateTicket()
    {
        
        $user = factory(\App\Models\Investor::class)->create(['is_verified'=>1]);

        $data = factory(\App\Models\Ticket::class)->make(
            ['owner_id'=> $user->id, 'owner_type'=>'App\Models\Investor']
        )->toArray();

        $data['message'] = \Faker\Factory::create()->sentence;

        $route = route('investors.ticket.store');

        $response = $this->actingAs($user, 'investor')->post($route, $data);

        // assert there was a redirect
        $response->assertStatus(302);

        //assert there was a success message
        $response->assertSessionHas(['success'=> 'Ticket created successfully']);

        $user->refresh();

        //assert user now has ticket
        $this->assertTrue($user->tickets->count() > 0);

        // assert a group notification was sent
        $this->assertDatabaseHas('group_notifications', 
            [
                'type'=> 'ticket',
                'entity_type'=> 'App\Models\TicketMessage',
            ]
        );
    }

    
    /**
     * @group Maintenance
     * 
     * @author Keania
     *
     * @return void
     */
    public function testInvestorCanCreateTicketWithFiles()
    {
        Storage::fake('public');
        
        $user = factory(\App\Models\Investor::class)->create(['is_verified'=>true]);

        $data = factory(\App\Models\Ticket::class)->make(
            ['owner_id'=> $user->id, 'owner_type'=> 'App\Models\Investor']
        )->toArray();

        $data['message'] = \Faker\Factory::create()->sentence;

        $data['file'] =  $file = UploadedFile::fake()->image('img2.jpg');

        $route = route('investors.ticket.store');

        $response = $this->actingAs($user, 'investor')->post($route, $data);
       
        // assert there was a redirect
        $response->assertStatus(302);

        //assert there was a success message
        $response->assertSessionHas(['success'=> 'Ticket created successfully']);

        $user->refresh();

        //assert user now has ticket
        $this->assertTrue($user->tickets->count() > 0);

        Storage::disk('public')->assertExists('documents/tickets/'.$file->hashName());
       

        // assert a group notification was sent
        $this->assertDatabaseHas('group_notifications', 
            [
                'type'=> 'ticket',
                'entity_type'=> 'App\Models\TicketMessage',
            ]
        );
    }


     /**
     * An authenticated Investor can respond to their ticket
     * 
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testInvestorCanRespondToTickets()
    {
        Storage::fake('public');
        
        $investor = factory(\App\Models\Investor::class)->create(['is_verified'=>true]);

        $ticket = factory(\App\Models\Ticket::class)->create(
            ['owner_id'=> $investor->id, 'owner_type'=> 'App\Models\Investor']
        );

        $data['message'] = \Faker\Factory::create()->sentence;

        $data['file'] =  $file = UploadedFile::fake()->image('img2.jpg');

        $route = route('investors.ticket.respond', ['ticket'=> $ticket->reference]);

        $response = $this->actingAs($investor, 'investor')->post($route, $data);

        //dd($response);

        // assert there was a redirect
        $response->assertStatus(302);

        //assert there was a success message
        $response->assertSessionHas(['success'=> 'Reply successfully sent']);

        Storage::disk('public')->assertExists('documents/tickets/'.$file->hashName());

        // assert that the message was created for the ticket
        $this->assertDatabaseHas('ticket_messages', 
            [
                'message'=> $data['message'],
                'owner_id'=> $investor->id,
                'owner_type'=> 'App\Models\Investor',
                'ticket_id'=> $ticket->id
            ]
        );

        // assert ticket status is now awating staff reply
        $this->assertEquals($ticket->refresh()->status, 1);

        // assert a group notification was sent
        $this->assertDatabaseHas('group_notifications', 
            [
                'type'=> 'ticket',
                'entity_type'=> 'App\Models\TicketMessage',
            ]
        );
    }


     /**
     * Test a user can successfully close tickets
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testUserCanCloseTicket()
    {
        $user = factory(\App\Models\Investor::class)->create();

        $ticket = factory(\App\Models\Ticket::class)->create();
        $ticket->refresh();
        $route = route('investors.ticket.close', ['ticket'=> $ticket->reference]);

        $response = $this->actingAs($user, 'investor')->post($route);

        $response->assertStatus(302);

        $this->assertTrue($ticket->refresh()->status == 3);
    }
}
