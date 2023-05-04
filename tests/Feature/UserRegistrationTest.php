<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    /**
     * Test if user can register.
     */
    public function test_if_user_can_register(): void
    {
        User::where('email','khan@mail.test')->delete();
        $response = $this->post('/user/auth/register', [
            'name'                  => 'Amer Khan',
            'email'                 => 'khan@mail.test',
            'phone'                 => '0931230414',
            'birth_date'            => '1999-08-21',
            'password'              => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test if user can register.
     */
    public function test_if_user_should_not_register_with_existing_email(): void
    {
        $response = $this->post('/user/auth/register', [
            'name'                  => 'Amer Khan',
            'email'                 => 'khan@mail.test',
            'phone'                 => '0931230314',
            'birth_date'            => '1999-08-21',
            'password'              => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(422);
    }
}
