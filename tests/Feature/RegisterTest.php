<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function name_is_required()
    {
        $this->postJson('/register', [
            'name' => '',
        ])->assertJsonValidationErrors('name');
    }

    /** @test **/
    public function email_is_required_and_must_be_unique()
    {
       $this->register([
           'email' => '',
       ])->assertJsonValidationErrors('email');

        User::factory()->create(['email' => 'john@example.com']);

        $this->register([
            'email' => 'john@example.com',
        ])->assertJsonValidationErrors('email');
    }

    /** @test **/
    public function password_length_must_be_at_least_6_chars_and_confirmed()
    {
        // min length
        $this->register([
            'password' => '123',
            'password_confirmation' => '123',
        ])->assertJsonValidationErrors('password');

        // not confirmed
        $this->register([
            'password' => '123456',
            'password_confirmation' => '1234567',
        ])->assertJsonValidationErrors('password');
    }

    /** @test **/
    public function user_type_is_required_and_is_in_whitelist()
    {
        // required
        $this->register([
            'type' => '',
        ])->assertJsonValidationErrors('type');

        //invalid type
        $this->register([
            'type' => 6, //  Allowed User::TYPE_CITIZEN, User::TYPE_LAWYER
        ])->assertJsonValidationErrors('type');
    }

    /** @test **/
    public function register_with_valid_data()
    {
        $this->assertEquals(0, User::count());

        $this->register([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'type' => User::TYPE_CITIZEN,
        ]);

        $this->assertEquals(1, User::count());
    }

    protected function register(array $attributes)
    {
        return $this->postJson('/register', $attributes);
    }
}
