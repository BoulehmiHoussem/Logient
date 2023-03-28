<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /**
     * Test for register feature.
     *
     * @return void
     */
    public function test_a_user_can_register()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'email' => $userData['email']
        ]);

        $this->assertAuthenticated();
    }

    /**
     * Test if user cannot register with empty credentials.
     *
     * @return void
     */
    public function test_a_user_cannot_register_with_empty_fields()
    {
        $userData = [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    /**
     * Test for login feature.
     *
     * @return void
     */
    public function test_a_user_can_login()
    {
        $password = 'password';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $userData = [
            'email' => $user->email,
            'password' => $password,
        ];

        $response = $this->post(route('login'), $userData);

        $response->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test if user cannot register with empty credentials.
     *
     * @return void
     */
    public function test_a_user_cannot_login_with_empty_fields()
    {
        $userData = [
            'email' => '',
            'password' => ''
        ];

        $response = $this->post(route('login'), $userData);

        $response->assertSessionHasErrors(['email', 'password']);
    }
}
