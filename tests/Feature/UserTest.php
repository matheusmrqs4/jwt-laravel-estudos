<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\WithFaker;

class UserTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     */
    public function testUserCanCreateAccount()
    {
        $userData = [
        'name' => $this->faker->name(),
        'email' => $this->faker->unique()->safeEmail(),
        'password' => 'password123',
        ];

        $response = $this->postJson("api/register", $userData);

        $response->assertStatus(200);
    }

    public function testUserCanLogin()
    {
        $user = User::factory()->create([
        'password' => bcrypt('password123'),
        ]);

        $credentials = [
        'email' => $user->email,
        'password' => 'password123',
        ];

        $response = $this->postJson("api/login", $credentials);

        $response->assertStatus(200);
    }

    public function testUserAuthenticatedCanLogout()
    {
        $user = User::factory()->create([
        'password' => bcrypt('password123'),
        ]);

        $token = JWTAuth::fromUser($user);

        $this->assertNotNull($token);

        $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        ])->postJson("api/auth/logout");

        $response->assertStatus(200);
    }

    public function testUnauthenticatedUserCannotLogout()
    {
        $response = $this->postJson("api/auth/logout");

        $response->assertStatus(401);
    }

    public function testUserAuthenticatedCanGetInfo()
    {
        $user = User::factory()->create([
        'password' => bcrypt('password123'),
        ]);

        $token = JWTAuth::fromUser($user);

        $this->assertNotNull($token);

        $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        ])->getJson("api/auth/me");

        $response->assertStatus(200);
    }

    public function testUnauthenticatedUserCannotGetInfo()
    {
        $response = $this->getJson("api/auth/me");

        $response->assertStatus(401);
    }
}
