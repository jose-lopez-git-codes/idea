<?php

use App\Models\User;
use App\Notifications\EmailChanged;

it('requires authentication', function () {
    $this->get(route('profile.edit'))->assertRedirect(route('login'));
});

it('edits a profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    visit(route('profile.edit'))
        ->assertSee('name')
        ->fill('name', 'New Name')
        ->assertSee('email')
        ->fill('email', 'newemail@example.com')
        ->click('Update Account')
        ->assertSee('Your profile has been updated.');

    expect($user->fresh())->toMatchArray([
        'name' => 'New Name',
        'email' => 'newemail@example.com',
    ]);
});

it('notifies the original email if updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Notification::fake();

    $originalEmail = $user->email;

    visit(route('profile.edit'))
        ->assertSee('email')
        ->fill('email', 'newemail@example.com')
        ->click('Update Account')
        ->assertSee('Your profile has been updated.');

    Notification::assertSentOnDemand(EmailChanged::class, fn (EmailChanged $notification, $routes, $notifiable) => $notifiable->routes['mail'] === $originalEmail);
});
