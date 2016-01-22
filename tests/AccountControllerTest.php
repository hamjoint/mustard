<?php

class AccountControllerTest extends TestCase
{
    public function testGuestIsRedirectedToLogin()
    {
        $this->get(action('\Hamjoint\Mustard\Http\Controllers\AccountController@index'))
            ->assertRedirectedTo('/login');
    }

    public function testIndexRedirectsToPassword()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AccountController@index'))
            ->assertRedirectedToAction('\Hamjoint\Mustard\Http\Controllers\AccountController@showChangePasswordForm');
    }

    public function testChangePasswordPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AccountController@showChangePasswordForm'))
            ->assertResponseOk();
    }

    public function testChangePasswordIncorrectPassword()
    {
        $user = factory(Hamjoint\Mustard\User::class)->make();

        $user->password = Hash::make('correct');

        $new_password = 'test_password';

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AccountController@showChangePasswordForm';

        $this->actingAs($user)
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AccountController@changePassword'), [
                'old_password' => 'incorrect',
                'new_password' => $new_password,
            ])
            ->assertRedirectedToAction($previous_url);

        // Check password hasn't been changed to the new one
        $this->assertFalse(Hash::check($new_password, $user->password));

        // Check the error was sent to the user
        $this->assertSessionHasErrors(['old_password' => 'mustard::account.password_incorrect']);
    }

    public function testChangePasswordSamePassword()
    {
        $user = factory(Hamjoint\Mustard\User::class)->make();

        $password = 'test_password';

        $user->password = Hash::make($password);

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AccountController@showChangePasswordForm';

        $this->actingAs($user)
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AccountController@changePassword'), [
                'old_password' => $password,
                'new_password' => $password,
            ])
            ->assertRedirectedToAction($previous_url);

        // Check the error was sent to the user
        $this->assertSessionHasErrors(['new_password' => 'mustard::account.password_same']);
    }

    public function testChangePasswordCorrectPassword()
    {
        $user = factory(Hamjoint\Mustard\User::class)->make();

        $user->password = Hash::make('test_password');

        $new_password = 'test_password2';

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AccountController@showChangePasswordForm';

        $this->actingAs($user)
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AccountController@changePassword'), [
                'old_password' => 'test_password',
                'new_password' => $new_password,
            ])
            ->assertRedirectedToAction($previous_url);

        // Check password was changed to the new one
        $this->assertTrue(Hash::check($new_password, $user->password));

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::account.password_changed');
    }

    public function testChangeEmailPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AccountController@showChangeEmailForm'))
            ->assertResponseOk();
    }

    public function testChangeEmailInvalidEmail()
    {
        $user = factory(Hamjoint\Mustard\User::class)->make();

        $email = $user->email;

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AccountController@showChangeEmailForm';

        $this->actingAs($user)
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AccountController@changeEmail'), [
                'email' => 'invalidemail',
            ])
            ->assertRedirectedToAction($previous_url);

        // Check email hasn't been changed to the new one
        $this->assertEquals($email, $user->email);

        // Check the error was sent to the user
        $this->assertSessionHasErrors('email');
    }

    public function testChangeEmailSameEmail()
    {
        $user = factory(Hamjoint\Mustard\User::class)->make();

        $email = $user->email;

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AccountController@showChangeEmailForm';

        $this->actingAs($user)
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AccountController@changeEmail'), [
                'email' => $email,
            ])
            ->assertRedirectedToAction($previous_url);

        // Check email hasn't been changed to the new one
        $this->assertEquals($email, $user->email);

        // Check the error was sent to the user
        $this->assertSessionHasErrors('email');
    }

    public function testChangeEmailExistingEmail()
    {
        $existing_user = factory(Hamjoint\Mustard\User::class)->create();

        $user = factory(Hamjoint\Mustard\User::class)->make();

        $email = $existing_user->email;

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AccountController@showChangeEmailForm';

        $this->actingAs($user)
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AccountController@changeEmail'), [
                'email' => $email,
            ])
            ->assertRedirectedToAction($previous_url);

        // Check email hasn't been changed to the new one
        $this->assertNotEquals($email, $user->email);

        // Check the error was sent to the user
        $this->assertSessionHasErrors('email');
    }

    public function testChangeEmailValidEmail()
    {
        $user = factory(Hamjoint\Mustard\User::class)->make();

        $email = 'test@example.com';

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AccountController@showChangeEmailForm';

        $this->actingAs($user)
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AccountController@changeEmail'), [
                'email' => $email,
            ])
            ->assertRedirectedToAction($previous_url);

        // Check email has been changed to the new one
        $this->assertEquals($email, $user->email);

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::account.email_changed');
    }

    public function testChangeNotificationsPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AccountController@showChangeNotificationsForm'))
            ->assertResponseOk();
    }

    public function testChangeNotifications()
    {
        $user = factory(Hamjoint\Mustard\User::class)->make();

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AccountController@showChangeNotificationsForm';

        $this->actingAs($user)
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AccountController@changeNotifications'), [
                'confirm' => 'garbage',
            ])
            ->assertRedirectedToAction($previous_url);

        // Check the error was sent to the user
        $this->assertSessionHasErrors('confirm');
    }

    public function testCloseAccountPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AccountController@showCloseAccountForm'))
            ->assertResponseOk();
    }

    public function testCloseAccountUnconfirmed()
    {
        $user = factory(Hamjoint\Mustard\User::class)->make();

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AccountController@showCloseAccountForm';

        $this->actingAs($user)
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AccountController@closeAccount'), [
                'confirm' => 'garbage',
            ])
            ->assertRedirectedToAction($previous_url);

        // Check the error was sent to the user
        $this->assertSessionHasErrors('confirm');
    }

    public function testCloseAccountConfirmed()
    {
        $user = factory(Hamjoint\Mustard\User::class)->make();

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AccountController@showCloseAccountForm';

        $this->actingAs($user)
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AccountController@closeAccount'), [
                'confirm' => 'close my account',
            ])
            ->assertRedirectedToAction($previous_url);

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::account.close_confirmed');
    }
}
