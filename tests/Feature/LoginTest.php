<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function user_can_sign_in_with_valid_credentails()
    {
        $user = User::factory()->create([
            'password' => bcrypt('123456789')
        ]);

        $this->assertGuest();

        $this->postJson('/login', [
            'email' => $user->email,
            'password' => '123456789',
        ]);

        $this->assertAuthenticated();
    }

    /** @test **/
    public function user_cannot_sign_in_with_invalid_credentails()
    {
        $user = User::factory()->create([
            'password' => bcrypt('123456789')
        ]);

        $this->assertGuest();
        $this->postJson('/login', [
            'email' => $user->email,
            'password' => '1234567891',
        ])->assertJsonValidationErrors('general');
        $this->assertGuest();
    }
}
