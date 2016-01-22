<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    use DatabaseMigrations;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        $app->config->set('app.debug', true);
        $app->config->set('app.key', md5('test'));

        $app->config->set('auth.defaults.verify_emails', 'users');
        $app->config->set('auth.provider.users.model', Hamjoint\Mustard\User::class);
        $app->config->set('auth.passwords.users.email', 'mustard::emails.password');
        $app->config->set('auth.passwords.users.table', 'email_tokens');
        $app->config->set('auth.verify_emails.users.provider', 'users');
        $app->config->set('auth.verify_emails.users.email', 'mustard::emails.verify');
        $app->config->set('auth.verify_emails.users.table', 'email_tokens');
        $app->config->set('auth.verify_emails.users.expire', 60);

        $app->config->set('database.default', 'mysql');
        $app->config->set('database.connections.mysql.driver', 'mysql');
        $app->config->set('database.connections.mysql.database', 'mustard_test');
        $app->config->set('database.connections.mysql.username', 'root');

        $app->config->set('mail.driver', 'log');
        $app->config->set('mail.from.address', 'test@localhost');

        $app->register('\Hamjoint\Mustard\Providers\MustardServiceProvider');

        $app[Kernel::class]->call('vendor:publish', ['--force' => true]);

        $app[EloquentFactory::class]->define(Hamjoint\Mustard\User::class, function (Faker\Generator $faker) {
            return [
                'username' => $faker->name,
                'email' => $faker->email,
                'password' => bcrypt(str_random(10)),
                'remember_token' => str_random(10),
            ];
        });

        return $app;
    }
}
