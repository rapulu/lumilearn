<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Events\TodoItemCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoInviteTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $owner;
    private $invitedUser;
    private $nonMemberUser;
    private $todo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();

        $this->invitedUser = User::factory()->create();

        $this->nonMemberUser = User::factory()->create();

        $this->todo = $this->owner->todos()->create(['title' => 'Lumilearn test']);

        $this->todo->members()->attach($this->invitedUser->id);
    }

    public function test_an_owner_can_invite_a_user_to_a_todo()
    {
        $inviteUser = User::factory()->create();
        $response = $this->actingAs($this->owner, 'sanctum')
                         ->postJson("/api/todos/{$this->todo->id}/invite", ['username' => $inviteUser->username]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'User invited successfully.']);

        $this->assertTrue($this->todo->members->contains($inviteUser));
    }


    public function test_an_invited_member_can_create_an_item_in_a_todo()
    {
       Event::fake();

        $item = ['name' => 'New Shared Task'];

        $response = $this->actingAs($this->invitedUser, 'sanctum')
                         ->postJson("/api/todos/{$this->todo->id}/items", $item);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'New Shared Task']);

        $this->assertDatabaseHas('todo_items', [
            'name' => 'New Shared Task',
            'todo_id' => $this->todo->id,
            'user_id' => $this->invitedUser->id,
        ]);

        Event::assertDispatched(TodoItemCreated::class, function ($event) {
            return $event->item->name === 'New Shared Task';
        });
    }

    public function test_a_non_member_cannot_create_an_item_in_a_todo()
    {

        $response = $this->actingAs($this->nonMemberUser, 'sanctum')
                         ->postJson("/api/todos/{$this->todo->id}/items", ['name' => 'Unauthorized member']);

        $response->assertStatus(403)
                 ->assertJson(['message' => 'You are not a member of this todo list.']);


        $this->assertDatabaseMissing('todo_items', [
            'name' => 'Unauthorized member',
        ]);
    }
}
