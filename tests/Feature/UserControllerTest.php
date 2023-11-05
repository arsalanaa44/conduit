<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_successful_login()
    {
        // Arrange
        $password = 'password';
        $user = User::factory()->create();
        $loginData = [
            'user' => [
                'email' => $user->email,
                'password' => $password,
            ]
        ];

        // Act
        $response = $this->postJson('/api/v1/users/login', $loginData);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'=>[
                    'user' => [
                        'email',
                        'token',
                        'username',
                        'bio',
                        'image'
                    ]
                ]
            ]);
    }

    public function test_unsuccessful_login()
    {
        // Arrange
        $loginData = [
            'user' => [
                'email' => 'nonexistentuser@example.com',
                'password' => 'wrongpassword',
            ]
        ];

        // Act
        $response = $this->postJson('/api/v1/users/login', $loginData);

        // Assert
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized']);
    }


    public function test_user_can_register()
    {
        // Arrange
        $userData = [
            'user' => [
                'username' => 'testuser',
                'email' => 'testuser@example.com',
                'password' => 'password123',
            ]
        ];

        // Act
        $response = $this->postJson('/api/v1/users', $userData);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'=>[

                    'user' => [
                    'email',
                    'token',
                    'username',
                    'bio',
                    'image'
                    ]
                ]
            ]);

        // Assert the user was created in the database
        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'testuser@example.com',
        ]);


    }
    public function test_registration_fails_with_duplicate_email()
    {
        // Arrange
        $user = User::factory()->create();
        $userData = [
            'user' => [
                'username' => 'testuser',
                'email' => $user->email,
                'password' => 'password123',
            ]
        ];

        // Act
        $response = $this->postJson('/api/v1/users', $userData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors('user.email');
    }

    public function test_registration_fails_with_duplicate_username()
    {
        // Arrange
        $user = User::factory()->create();
        $userData = [
            'user' => [
                'username' => $user->username,
                'email' => 'testuser@example.com',
                'password' => 'password123',
            ]
        ];

        // Act
        $response = $this->postJson('/api/v1/users', $userData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors('user.username');
    }

    
}
