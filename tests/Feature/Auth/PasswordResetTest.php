<?php

use App\Models\User;

test('reset password screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('password can be reset with valid secret code', function () {
    $user = User::factory()->create();

    $response = $this->post('/forgot-password', [
        'username' => $user->username,
        'secret_code' => env('RESET_SECRET_CODE'),
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('login'));
});

test('password reset fails with invalid secret code', function () {
    $user = User::factory()->create();

    $response = $this->post('/forgot-password', [
        'username' => $user->username,
        'secret_code' => 'invalid_code',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ]);

    $response->assertSessionHasErrors();
});
