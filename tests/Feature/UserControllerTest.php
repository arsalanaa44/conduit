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


}
//    protected function setUp(): void
//    {
//        parent::setUp();
//    }
//
//    public function test_user_can_register()
//    {
//        $userData = [
//            'user' => [
//                'username' => $this->faker->userName,
//                'email' => $this->faker->safeEmail,
//                'password' => 'password123',
//            ]
//        ];
//
//        $response = $this->postJson('/api/v1/users/', $userData);
//
//        $response->assertStatus(200 );
////            ->assertJsonStructure(['data' => ['id', 'username', 'email', 'created_at', 'updated_at', 'token']]);
//    }
//
//    public function test_user_can_login()
//    {
//        $password = 'password123';
//        $user = User::factory()->create(['password' => bcrypt($password)]);
//
//        $loginData = [
//            'user' => [
//                'email' => $user->email,
//                'password' => $password,
//            ]
//        ];
//
//        $response = $this->postJson('/api/v1/users/login', $loginData);
//
//        $response->assertStatus(200)
//            ->assertJsonStructure(['data' => ['id', 'username', 'email', 'created_at', 'updated_at', 'token']]);
//    }
//
//    public function test_user_can_get_current_user()
//    {
//        $user = User::factory()->create();
//        $token = $user->createToken('test')->plainTextToken;
//
//        $response = $this->withHeader('Authorization', 'Bearer '.$token)->getJson('/api/v1/user/');
//
//        $response->assertStatus(200)
//            ->assertJson(['data' => ['id' => $user->id, 'username' => $user->username, 'email' => $user->email]]);
//    }
//
//    public function test_user_can_update_current_user()
//    {
//        $user = User::factory()->create();
//        $token = $user->createToken('test')->plainTextToken;
//
//        $updateData = [
//            'user' => [
//                'username' => $this->faker->userName,
//                'email' => $this->faker->safeEmail,
//                'password' => 'newpassword123',
//            ]
//        ];
//
//        $response = $this->withHeader('Authorization', 'Bearer '.$token)->putJson('/api/v1/user/', $updateData);
//
//        $response->assertStatus(200)
//            ->assertJson(['data' => ['username' => $updateData['user']['username'], 'email' => $updateData['user']['email']]]);
//    }
//}
