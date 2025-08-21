<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_a_user_can_create_a_todos()
    {
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/todos', ['title' => 'My New Todo']);

        $response->assertStatus(201)
                 ->assertJson(['title' => 'My New Todo']);

        $this->assertDatabaseHas('todos', ['title' => 'My New Todo']);
    }

    public function test_a_user_can_view_their_todos()
    {
        $todo = $this->user->todos()->create(['title' => 'My First Todo']);
        $todo->items()->create(['user_id' => $this->user->id, 'name' => 'Test Item']);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson('/api/todos');

        $response->assertStatus(200)
                 ->assertJsonCount(1);
    }
}
