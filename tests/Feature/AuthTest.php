<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_a_user_can_register()
    {
        $user = [
            'name' => $this->faker->name,
            'username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'type' => 'user',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $user);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('users', ['email' => $user['email']]);
    }


    public function test_a_user_can_login()
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $login = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $login);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }
}
