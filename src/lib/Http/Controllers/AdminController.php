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
use Hamjoint\Mustard\Item;
use Hamjoint\Mustard\Tables\AdminItems;
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
            'User stats' => [
                'Registered' => function($range)
                {
                    return mustard_number(User::totalRegistered($range));
                },
                'Bidders' => function($range)
                {
                    return mustard_number(User::totalBidders($range));
                },
                'Buyers' => function($range)
                {
                    return mustard_number(User::totalBuyers($range));
                },
                'Sellers' => function($range)
                {
                    return mustard_number(User::totalSellers($range));
                },
            ],
            'Item stats' => [
                'Listed' => function($range)
                {
                    return mustard_number(Item::totalListed($range));
                },
                'Bids placed' => function($range)
                {
                    return mustard_number(\Hamjoint\Mustard\Auctions\Bid::totalPlaced($range));
                },
                'Average bid amount' => function($range)
                {
                    return mustard_price(\Hamjoint\Mustard\Auctions\Bid::averageAmount($range));
                },
                'Watched' => function($range)
                {
                    return mustard_number(Item::totalWatched($range));
                },
            ],
            'Transaction stats' => [
                'Purchases' => function($range)
                {
                    return mustard_number(\Hamjoint\Mustard\Commerce\Purchase::totalCreated($range));
                },
                'Average amount' => function($range)
                {
                    return mustard_price(\Hamjoint\Mustard\Commerce\Purchase::averageAmount($range));
                },
            ],
        ];

        $ranges = [
            'Today' => strtotime('midnight'),
            'This week' => strtotime('monday this week'),
            'This month' => strtotime('midnight first day of this month'),
            'This year' => strtotime(date('Y') . '/01/01'),
            'Overall' => 0,
        ];

        return view('mustard::admin.dashboard', [
            'ranges' => $ranges,
            'stats' => $stats,
        ]);
    }

    /**
     * Return the admin items view.
     *
     * @return \Illuminate\View\View
     */
    public function getItems(Request $request)
    {
        $table = new AdminItems(Item::query(), $request);

        return view('mustard::admin.items', [
            'table' => $table,
        ]);
    }

    /**
     * Return the admin users view.
     *
     * @return \Illuminate\View\View
     */
    public function getUsers(Request $request)
    {
        $table = new AdminUsers(User::query(), $request);

        return view('mustard::admin.users', [
            'table' => $table,
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
                'users' => 'required',
                'subject' => 'required|min:4',
                'body' => 'required|min:10',
            ]
        );

        $count = 0;

        foreach (User::all() as $user) {
            if (in_array($user->userId, $request->input('users'))) {
                $user->sendEmail(
                    $request->input('subject'),
                    'emails.mailout',
                    [
                        'body' => $request->input('body'),
                        'handle' => $user->getHandle(),
                        'email' => $user->email,
                        'joined' => $user->joined,
                    ]
                );

                $count++;
            }
        }

        return redirect()->back()->withMessage("Mailout sent to $count recipients.");
    }
}
