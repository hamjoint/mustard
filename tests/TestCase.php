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

        $app->config->set('database.default', 'mysql');
        $app->config->set('database.connections.mysql.driver', 'mysql');
        $app->config->set('database.connections.mysql.database', 'mustard_test');
        $app->config->set('database.connections.mysql.username', 'root');

        $app->config->set('mail.driver', 'log');
        $app->config->set('mail.from.address', 'test@localhost');

        $app->register('\Hamjoint\Mustard\Providers\MustardServiceProvider');

        $app[Kernel::class]->call('vendor:publish', ['--force' => true]);

        $app[EloquentFactory::class]->define(Hamjoint\Mustard\Category::class, function (Faker\Generator $faker) {
            return [
                'name' => implode(' ', $faker->words),
                'slug' => $faker->word,
                'sort' => $faker->randomDigitNotNull,
            ];
        });

        $app[EloquentFactory::class]->define(Hamjoint\Mustard\DeliveryOption::class, function (Faker\Generator $faker) {
            return [
                'name' => implode(' ', $faker->words),
                'slug' => $faker->word,
                'sort' => $faker->randomDigitNotNull,
            ];
        });

        $app[EloquentFactory::class]->define(Hamjoint\Mustard\Item::class, function (Faker\Generator $faker) {
            $seller = factory(Hamjoint\Mustard\User::class)->create();

            $duration = factory(Hamjoint\Mustard\ListingDuration::class)->create();

            $start_price = mt_rand(50, 500000) / 100;

            $created = mt_rand($seller->joined, time());

            $start_date = mt_rand($created, time() + 86400 * 14);

            return [
                'name'                => implode(' ', $faker->words(mt_rand(2, 5))),
                'description'         => implode("\n\n", $faker->paragraphs(2)),
                'auction'             => false,
                'quantity'            => mt_rand(1, 100),
                'start_price'         => $start_price,
                'bidding_price'       => null,
                'reserve_price'       => mt_rand($start_price * 100, 500000) / 100,
                'fixed_price'         => !mt_rand(0, 3) ? $start_price + mt_rand(50, 500000) / 100 : 0,
                'commission'          => mt_rand(0, 100) / 100,
                'duration'            => $duration->duration,
                'created'             => $created,
                'start_date'          => $start_date,
                'end_date'            => $start_date + $duration->duration,
                'collection_location' => $faker->city,
                'payment_other'       => mt_rand(0, 1),
                'returns_period'      => mt_rand(7, 21),
                'user_id'             => $seller->userId,
                'item_condition_id'   => factory(Hamjoint\Mustard\ItemCondition::class)->create()->itemConditionId,
            ];
        });

        $app[EloquentFactory::class]->define(Hamjoint\Mustard\ItemCondition::class, function (Faker\Generator $faker) {
            return [
                'name' => $faker->word,
            ];
        });

        $app[EloquentFactory::class]->define(Hamjoint\Mustard\ListingDuration::class, function (Faker\Generator $faker) {
            return [
                'duration' => mt_rand(3600, 3600 * 24 * 14),
            ];
        });

        $app[EloquentFactory::class]->define(Hamjoint\Mustard\User::class, function (Faker\Generator $faker) {
            $joined = mt_rand(time() - mt_rand(0, 86400 * 200), time());

            return [
                'username'       => $faker->userName,
                'email'          => $faker->email,
                'password'       => Hash::make(str_random(10)),
                'joined'         => $joined,
                'locale'         => $faker->locale,
                'currency'       => $faker->currencyCode,
                'last_login'     => mt_rand($joined, time()),
                'remember_token' => str_random(10),
            ];
        });

        return $app;
    }
}
