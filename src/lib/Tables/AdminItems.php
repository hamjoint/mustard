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

namespace Hamjoint\Mustard\Tables;

use DB;
use Foundation\Pagination\FoundationFivePresenter;
use Tablelegs\Table;

class AdminItems extends Table
{
    /**
     * Column headers for the table. URL-friendly keys with human values.
     *
     * @var array
     */
    public $columnHeaders = [
        'Item ID'     => 'item_id',
        'Name'        => 'name',
        'Seller'      => 'seller',
        'Starting in' => 'starting_in',
        'Time left'   => 'time_left',
    ];

    /**
     * Array of filter names containing available options and their keys.
     *
     * @var array
     */
    public $filters = [
        'State' => [
            'Ended',
            'Active',
            'Scheduled',
        ],
        'Type' => [
            'Auction',
            'Fixed',
        ],
    ];

    /**
     * Default key to sort by.
     *
     * @var string
     */
    public $defaultSortKey = 'time_left';

    /**
     * Class name for the paginator presenter.
     *
     * @var string
     */
    public $presenter = FoundationFivePresenter::class;

    /**
     * Include ended items only.
     *
     * @return void
     */
    public function filterStateEnded()
    {
        $this->db->ended();
    }

    /**
     * Include active items only.
     *
     * @return void
     */
    public function filterStateActive()
    {
        $this->db->active();
    }

    /**
     * Include scheduled items only.
     *
     * @return void
     */
    public function filterStateScheduled()
    {
        $this->db->scheduled();
    }

    /**
     * Include auction-type items only.
     *
     * @return void
     */
    public function filterTypeAuction()
    {
        $this->db->typeAuction();
    }

    /**
     * Include fixed-type items only.
     *
     * @return void
     */
    public function filterTypeFixed()
    {
        $this->db->typeFixed();
    }

    /**
     * Sort by sellers' usernames.
     *
     * @return void
     */
    public function sortSeller($sortOrder)
    {
        $this->db->join('users', 'items.user_id', '=', 'users.user_id');

        $this->db->sort('users.username', $sortOrder);
    }

    /**
     * Sort by time until item starts.
     *
     * @return void
     */
    public function sortStartingIn($sortOrder)
    {
        $this->db->sort(DB::raw('cast(`start_date` as signed) - unix_timestamp()'), $sortOrder);
    }

    /**
     * Sort by time until item ended.
     *
     * @return void
     */
    public function sortTimeLeft($sortOrder)
    {
        $this->db->sort(DB::raw('cast(`end_date` as signed) - unix_timestamp()'), $sortOrder);
    }
}
