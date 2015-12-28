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

namespace Hamjoint\Mustard\Http\Controllers;

use Cache;
use DB;
use Hamjoint\Mustard\Category;
use Hamjoint\Mustard\Item;
use Hamjoint\Mustard\ItemCondition;
use Hamjoint\Mustard\ListingDuration;
use Hamjoint\Mustard\Tables\AdminCategories;
use Hamjoint\Mustard\Tables\AdminItemConditions;
use Hamjoint\Mustard\Tables\AdminItems;
use Hamjoint\Mustard\Tables\AdminListingDurations;
use Hamjoint\Mustard\Tables\AdminSettings;
use Hamjoint\Mustard\Tables\AdminUsers;
use Hamjoint\Mustard\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Redirect index requests to the dashboard page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getIndex()
    {
        return mustard_redirect('/admin/dashboard');
    }

    /**
     * Return the admin dashboard view.
     *
     * @return \Illuminate\View\View
     */
    public function getDashboard()
    {
        $stats = [
            'Item stats' => [
                'Listed' => function ($range) {
                    return mustard_number(Cache::remember(
                        'total_items',
                        config('mustard.dashboard_cache'),
                        function () use ($range) {
                            return Item::totalListed($range);
                        }
                    ));
                },
                'Watched' => function ($range) {
                    return mustard_number(Cache::remember(
                        'total_items',
                        config('mustard.dashboard_cache'),
                        function () use ($range) {
                            return Item::totalWatched($range);
                        }
                    ));
                },
            ],
            'User stats' => [
                'Registered' => function ($range) {
                    return mustard_number(Cache::remember(
                        'total_users',
                        config('mustard.dashboard_cache'),
                        function () use ($range) {
                            return User::totalRegistered($range);
                        }
                    ));
                },
                'Sellers' => function ($range) {
                    return mustard_number(Cache::remember(
                        'total_sellers',
                        config('mustard.dashboard_cache'),
                        function () use ($range) {
                            return User::totalSellers($range);
                        }
                    ));
                },
            ],
        ];

        if (mustard_loaded('auctions')) {
            $stats['User stats']['Bidders'] = function ($range) {
                return mustard_number(Cache::remember(
                    'total_bidders',
                    config('mustard.dashboard_cache'),
                    function () use ($range) {
                        return User::totalBidders($range);
                    }
                ));
            };

            $stats['Item stats']['Bids placed'] = function ($range) {
                return mustard_number(Cache::remember(
                    'total_bids_placed',
                    config('mustard.dashboard_cache'),
                    function () use ($range) {
                        return \Hamjoint\Mustard\Auctions\Bid::totalPlaced($range);
                    }
                ));
            };

            $stats['Item stats']['Average bid amount'] = function ($range) {
                return mustard_price(Cache::remember(
                    'average_bids',
                    config('mustard.dashboard_cache'),
                    function () use ($range) {
                        return \Hamjoint\Mustard\Auctions\Bid::averageAmount($range);
                    }
                ));
            };
        }

        if (mustard_loaded('commerce')) {
            $stats['User stats']['Buyers'] = function ($range) {
                return mustard_number(Cache::remember(
                    'total_buyers',
                    config('mustard.dashboard_cache'),
                    function () use ($range) {
                        return User::totalBuyers($range);
                    }
                ));
            };

            $stats['Transaction stats']['Purchases'] = function ($range) {
                return mustard_number(Cache::remember(
                    'total_purchases',
                    config('mustard.dashboard_cache'),
                    function () use ($range) {
                        return \Hamjoint\Mustard\Commerce\Purchase::totalCreated($range);
                    }
                ));
            };

            $stats['Transaction stats']['Average amount'] = function ($range) {
                return mustard_price(Cache::remember(
                    'average_purchases',
                    config('mustard.dashboard_cache'),
                    function () use ($range) {
                        return \Hamjoint\Mustard\Commerce\Purchase::averageAmount($range);
                    }
                ));
            };
        }

        $ranges = [
            'Today'      => strtotime('midnight'),
            'This week'  => strtotime('monday this week'),
            'This month' => strtotime('midnight first day of this month'),
            'This year'  => strtotime(date('Y').'/01/01'),
            'Overall'    => 0,
        ];

        return view('mustard::admin.dashboard', [
            'ranges' => $ranges,
            'stats'  => $stats,
        ]);
    }

    /**
     * Return the admin categories view.
     *
     * @return \Illuminate\View\View
     */
    public function getCategories()
    {
        $categories = Category::query()
            ->leftJoin('items')
            ->addSelect(DB::raw('COUNT(items.item_id) as item_count'))
            ->groupBy('categories.category_id');

        $table = new AdminCategories($categories);

        $table->with('parent');

        return view('mustard::admin.categories', [
            'table'      => $table,
            'categories' => $table->paginate(),
        ]);
    }

    /**
     * Return the admin items view.
     *
     * @return \Illuminate\View\View
     */
    public function getItems()
    {
        $table = new AdminItems(Item::query());

        $table->with('seller');

        if (mustard_loaded('feedback')) {
            $table->with('seller.feedbackReceived');
        }

        return view('mustard::admin.items', [
            'table' => $table,
            'items' => $table->paginate(),
        ]);
    }

    /**
     * Return the admin item conditions view.
     *
     * @return \Illuminate\View\View
     */
    public function getItemConditions()
    {
        $table = new AdminItemConditions(ItemCondition::query());

        return view('mustard::admin.item-conditions', [
            'table'           => $table,
            'item_conditions' => $table->paginate(),
        ]);
    }

    /**
     * Return the admin listing durations view.
     *
     * @return \Illuminate\View\View
     */
    public function getListingDurations()
    {
        $table = new AdminListingDurations(ListingDuration::query());

        return view('mustard::admin.listing-durations', [
            'table'             => $table,
            'listing_durations' => $table->paginate(),
        ]);
    }

    /**
     * Return the admin users view.
     *
     * @return \Illuminate\View\View
     */
    public function getUsers()
    {
        $table = new AdminUsers(User::query());

        if (mustard_loaded('feedback')) {
            $table->with('feedbackReceived');
        }

        return view('mustard::admin.users', [
            'table' => $table,
            'users' => $table->paginate(),
        ]);
    }

    /**
     * Return the admin settings view.
     *
     * @return \Illuminate\View\View
     */
    public function getSettings()
    {
        $config = config('mustard');

        array_walk($config, function (&$value) {
            if (!is_scalar($value) || is_bool($value)) {
                $value = var_export($value, true);
            }
        });

        $table = new AdminSettings($config);

        return view('mustard::admin.settings', [
            'table'    => $table,
            'settings' => $table->paginate(),
        ]);
    }

    /**
     * Return the admin mailout view.
     *
     * @return \Illuminate\View\View
     */
    public function getMailout()
    {
        return view('mustard::admin.mailout', [
            'users' => User::orderBy('username', 'asc')->get(),
        ]);
    }

    /**
     * Send a mailout.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postMailout(Request $request)
    {
        $this->validates(
            $request->all(),
            [
                'users'   => 'required',
                'subject' => 'required|min:4',
                'body'    => 'required|min:10',
            ]
        );

        $count = 0;

        foreach (User::all() as $user) {
            if (in_array($user->userId, $request->input('users'))) {
                $user->sendEmail(
                    $request->input('subject'),
                    'emails.mailout',
                    [
                        'body'   => $request->input('body'),
                        'handle' => $user->getHandle(),
                        'email'  => $user->email,
                        'joined' => $user->joined,
                    ]
                );

                $count++;
            }
        }

        return redirect()->back()->withStatus("Mailout sent to $count recipients.");
    }
}
