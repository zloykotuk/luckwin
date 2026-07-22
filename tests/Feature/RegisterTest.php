<?php

use App\Models\User;

it('registers a new user and returns a lucky link', function () {
    $response = $this->postJson('/register', [
        'username' => 'johndoe123',
        'phonenumber' => '+1 202-555-0143',
    ]);

    $response->assertCreated();
    $response->assertJsonStructure(['data' => ['hash', 'url']]);

    $this->assertDatabaseHas('users', [
        'username' => 'johndoe123',
        'phonenumber' => '12025550143',
    ]);

    $user = User::where('username', 'johndoe123')->firstOrFail();

    $this->assertDatabaseHas('links', ['user_id' => $user->id]);
});

it('rejects a duplicate username', function () {
    User::factory()->create(['username' => 'johndoe123']);

    $response = $this->postJson('/register', [
        'username' => 'johndoe123',
        'phonenumber' => '+1 202-555-0144',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('username');
});

it('rejects a duplicate phone number regardless of input formatting', function () {
    User::factory()->create(['phonenumber' => '12025550143']);

    $response = $this->postJson('/register', [
        'username' => 'someoneelse',
        'phonenumber' => '+1 (202) 555-0143',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('phonenumber');
});

it('rejects usernames with characters other than letters and digits', function () {
    $response = $this->postJson('/register', [
        'username' => 'john_doe!',
        'phonenumber' => '+12025550143',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('username');
});

it('rejects an invalid phone number', function () {
    $response = $this->postJson('/register', [
        'username' => 'johndoe123',
        'phonenumber' => 'not-a-phone',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('phonenumber');
});
