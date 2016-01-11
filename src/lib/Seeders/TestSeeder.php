<?php

/*

This file is part of Mustard.

Mustard is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Mustard is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Mustard.  If not, see <http://www.gnu.org/licenses/>.

*/

namespace Hamjoint\Mustard\Seeders;

use DB;
use Faker\Factory as FakerFactory;
use Hamjoint\Mustard\Category;
use Hamjoint\Mustard\Item;
use Hamjoint\Mustard\ItemCondition;
use Hamjoint\Mustard\ListingDuration;
use Hamjoint\Mustard\Model;
use Hamjoint\Mustard\User;
use Hash;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Total categories to generate.
     */
    const TOTAL_CATEGORIES = 40;

    /**
     * Total items to generate.
     */
    const TOTAL_ITEMS = 1000;

    /**
     * Total users to generate.
     */
    const TOTAL_USERS = 200;

    /**
     * Faker object for generating data.
     *
     * @var \Faker\Factory
     */
    private $faker;

    /**
     * Cache of generated categories.
     *
     * @var array
     */
    private $categories = [];

    /**
     * Cache of generated item conditions.
     *
     * @var array
     */
    private $itemConditions = [];

    /**
     * Cache of generated listing durations.
     *
     * @var array
     */
    private $listingDurations = [];

    /**
     * Cache of generated users.
     *
     * @var array
     */
    private $users = [];

    /**
     * The time.
     *
     * @var int
     */
    private $now = null;

    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = FakerFactory::create('en_GB');

        // Returns a random element from an array

        function mt_rand_arr($array, $exclude = [])
        {
            if ($exclude) {
                $array = array_diff($array, $exclude);
            }

            return $array[mt_rand(0, count($array) - 1)];
        }

        DB::connection()->disableQueryLog();

        $this->now = time();

        $this->itemConditions = ItemCondition::all();

        $this->listingDurations = ListingDuration::all();

        $this->command->info('Adding categories');

        for ($i = 1; $i <= self::TOTAL_CATEGORIES; $i++) {
            $category = new Category();

            $category->name = implode(' ', $this->faker->words(2));
            $category->slug = str_slug($category->name);
            $category->sort = mt_rand(0, 10);

            $category->save();

            $this->categories[] = $category;
        }

        foreach ($this->categories as $category) {
            if (mt_rand(0, 1)) {
                continue;
            }

            $category->parentCategoryId = mt_rand_arr($this->categories)->categoryId;

            $category->save();
        }

        $this->command->info('Adding users');

        for ($i = 1; $i <= self::TOTAL_USERS; $i++) {
            $user = new User();

            $user->username = $this->faker->userName; while (User::findByEmail($user->email = $this->faker->email));
            $user->password = Hash::make('password');
            //$user->verified = true;
            $user->joined = mt_rand($this->now - mt_rand(0, 86400 * 200), $this->now);
            $user->locale = $this->faker->locale;
            $user->currency = $this->faker->currencyCode;
            $user->lastLogin = mt_rand($user->joined, $this->now);

            $user->save();

            //if ($i == 1) $user->grantAdmin();

            $this->users[] = $user;
        }

        if (mustard_loaded('commerce')) {
            $this->command->info('Adding postal addresses');

            foreach ($this->users as $user) {
                for ($i = 0; $i <= mt_rand(1, 3); $i++) {
                    $postal_address = new \Hamjoint\Mustard\Commerce\PostalAddress();

                    $postal_address->user()->associate($user);

                    $postal_address->name = $user->username;
                    $postal_address->street1 = $this->faker->secondaryAddress;
                    $postal_address->street2 = $this->faker->streetAddress;
                    $postal_address->city = $this->faker->city;
                    $postal_address->county = $this->faker->county;
                    $postal_address->postcode = $this->faker->postcode;
                    $postal_address->country = $this->faker->countryCode;

                    $postal_address->save();
                }
            }
        }

        $this->command->info('Adding items');

        for ($i = 1; $i <= self::TOTAL_ITEMS; $i++) {
            $item = new Item();

            $item->seller()->associate(mt_rand_arr($this->users));
            $item->condition()->associate($this->itemConditions->random());

            $item->name = implode(' ', $this->faker->words(mt_rand(2, 5)));
            $item->description = implode("\n\n", $this->faker->paragraphs(2));
            $item->auction = mustard_loaded('auctions') ? (bool) mt_rand(0, 1) : false;
            $item->quantity = !$item->auction ? mt_rand(1, 100) : 1;
            $item->startPrice = $item->biddingPrice = mt_rand(50, 500000) / 100;
            $item->reservePrice = mt_rand($item->startPrice * 100, 500000) / 100;
            $item->fixedPrice = (!$item->auction || !mt_rand(0, 3))
                ? $item->startPrice + mt_rand(50, 500000) / 100
                : 0;
            $item->commission = 0.07;
            $item->duration = $this->listingDurations->random()->duration;
            $item->created = mt_rand($item->seller->joined, $this->now);
            $item->startDate = mt_rand($item->created, $this->now + 86400 * 14);
            $item->endDate = $item->getEndDate();
            $item->collectionLocation = $this->faker->city;
            $item->paymentOther = mt_rand(0, 1);
            $item->returnsPeriod = mt_rand(7, 21);

            $item->save();

            // Add to categories
            for ($ii = 0; $ii < mt_rand(1, 3); $ii++) {
                $category = Category::leaves()->get()->random();

                try {
                    $item->categories()->save($category);
                } catch (\Exception $e) {
                    //
                }
            }

            // Add delivery options
            for ($ii = 0; $ii < mt_rand(0, 3); $ii++) {
                $delivery_option = new \Hamjoint\Mustard\DeliveryOption();

                $delivery_option->name = implode(' ', $this->faker->words(3));
                $delivery_option->price = mt_rand(50, 1000) / 100;
                $delivery_option->min_arrival_time = mt_rand(2, 10);
                $delivery_option->max_arrival_time = mt_rand(
                    $delivery_option->min_arrival_time,
                    $delivery_option->min_arrival_time + 20
                );

                $delivery_option->item()->associate($item);

                $delivery_option->save();
            }

            $watchers = [];

            // Add watchers
            for ($ii = 0; $ii < mt_rand(0, 3); $ii++) {
                $watchers[] = mt_rand_arr($this->users);
            }

            foreach (array_unique($watchers) as $watcher) {
                $item->watchers()->save($watcher, [
                    'added' => mt_rand($item->startDate, $item->endDate),
                ]);
            }
        }

        if (mustard_loaded('auctions')) {
            $this->command->info('Adding bids');

            foreach (Item::where('auction', true)->where('start_date', '<=', $this->now)->get() as $item) {
                $bid_amount = $item->startPrice;
                $bid_time = $item->startDate;
                $end_time = $item->endDate < $this->now ? $item->endDate : $this->now;

                for ($i = 0; $i < mt_rand(0, 10); $i++) {
                    if ($bid_time == $end_time) {
                        break;
                    }

                    $bid = new \Hamjoint\Mustard\Auctions\Bid();

                    do {
                        $user = mt_rand_arr($this->users);
                    } while ($user->userId == $item->seller->userId);

                    $bid->bidder()->associate($user);
                    $minimum_bid = \Hamjoint\Mustard\Auctions\BidIncrement::getMinimumNextBid($bid_amount);
                    $bid->amount = $bid_amount = ($i == 0)
                        ? $item->startPrice
                        : (mt_rand($minimum_bid * 100, ($minimum_bid + $bid_amount) * 100) / 100);
                    $bid->placed = $bid_time = mt_rand($bid_time, $end_time);

                    $item->bids()->save($bid);
                }

                $last_bids = $item->bids()->orderBy('placed', 'desc')->take(2)->get();

                if ($last_bids->count()) {
                    $item->biddingPrice = \Hamjoint\Mustard\Auctions\BidIncrement::getMinimumNextBid($last_bids->last()->amount);
                }

                if (!$item->isActive()) {
                    $bid = $item->bids()
                        ->where('amount', DB::raw('(select max(`amount`) from `bids` where `item_id` = '.$item->itemId.')'))
                        ->first();

                    if ($bid) {
                        $item->winningBid()->associate($bid);
                    }
                }

                $item->save();
            }
        }

        if (mustard_loaded('commerce')) {
            $this->command->info('Adding purchases');

            foreach (Item::all() as $item) {
                if ($item->auction && $item->isEnded()) {
                    $item->end();
                }

                if (!$item->isStarted() || mustard_loaded('auctions') && $item->auction && !$item->winningBid) {
                    continue;
                }

                if (mt_rand(0, 5)) {
                    $purchase = new \Hamjoint\Mustard\Commerce\Purchase();

                    $purchase->item()->associate($item);
                    $purchase->buyer()->associate(
                        mustard_loaded('auctions') && $item->auction ? $item->winningBid->bidder : mt_rand_arr($this->users)
                    );

                    if ($item->deliveryOptions->count() && $delivery_option = $item->deliveryOptions->random()) {
                        $purchase->deliveryOption()->associate($delivery_option);
                    }

                    $purchase->useAddress($purchase->buyer->postalAddresses->random());

                    $purchase->created = mt_rand($item->startDate, min($item->endDate, $this->now));
                    $purchase->unitPrice = $purchase->total = $item->biddingPrice;
                    $purchase->quantity = 1;

                    if (mt_rand(0, 5)) {
                        $purchase->received = $purchase->grandTotal;
                        $purchase->paid = mt_rand($purchase->created, $this->now);

                        if (mt_rand(0, 2)) {
                            $purchase->dispatched = mt_rand($purchase->paid, $this->now);

                            if (mt_rand(0, 2)) {
                                $purchase->trackingNumber = str_random(16);
                            }

                            if (mt_rand(0, 1)) {
                                $purchase->refunded = mt_rand($purchase->dispatched, $this->now);

                                if (mt_rand(0, 1)) {
                                    $purchase->refundedAmount = $purchase->grandTotal;
                                } else {
                                    $purchase->refundedAmount = mt_rand(1, $purchase->grandTotal);
                                }
                            }
                        }
                    }

                    $purchase->save();
                }
            }
        }

        if (mustard_loaded('feedback')) {
            $this->command->info('Adding feedback');

            foreach (\Hamjoint\Mustard\Commerce\Purchase::all() as $purchase) {
                for ($i = 0; $i < 1; $i++) {
                    if (!mt_rand(0, 5)) {
                        continue;
                    }

                    $rater = $purchase->buyer;
                    $subject = $purchase->item->seller;

                    if ($i) {
                        list($rater, $subject) = [$subject, $rater];
                    }

                    $feedback = new \Hamjoint\Mustard\Feedback\UserFeedback();

                    $feedback->rating = mt_rand(0, 10) - 5;
                    $feedback->message = $this->faker->sentence;
                    $feedback->placed = mt_rand($purchase->created, $this->now);

                    $feedback->rater()->associate($rater);
                    $feedback->subject()->associate($subject);
                    $feedback->purchase()->associate($purchase);

                    $feedback->save();
                }
            }
        }

        if (mustard_loaded('messaging')) {
            $this->command->info('Adding messages');

            foreach (User::all() as $user) {
                for ($i = 0; $i < mt_rand(5, 50); $i++) {
                    $message = new \Hamjoint\Mustard\Messaging\Message();

                    $message->subject = $this->faker->sentence;
                    $message->body = implode("\n\n", $this->faker->paragraphs(2));

                    $recipient = mt_rand_arr($this->users, [$user]);
                    $sender = $user;

                    $message->sent = mt_rand(max($sender->joined, $recipient->joined), $this->now);
                    $message->read = (bool) mt_rand(0, 1);

                    $message->recipient()->associate($recipient);
                    $message->sender()->associate($sender);

                    $message->user()->associate($sender);

                    $recipient_copy = clone $message;

                    $recipient_copy->user()->associate($recipient);

                    $recipient_copy->save();

                    $message->save();
                }
            }
        }
    }
}
