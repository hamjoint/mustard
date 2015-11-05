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
    public $columnHeaders = [
        'Item ID' => 'item_id',
        'Name' => 'name',
        'Seller' => 'seller',
        'Starting in' => 'starting_in',
        'Time left' => 'time_left',
    ];

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

    public $defaultSortKey = 'time_left';

    public $presenter = FoundationFivePresenter::class;

    public function filterStateEnded()
    {
        $this->db->ended();
    }

    public function filterStateActive()
    {
        $this->db->active();
    }

    public function filterStateScheduled()
    {
        $this->db->scheduled();
    }

    public function filterTypeAuction()
    {
        $this->db->typeAuction();
    }

    public function filterTypeFixed()
    {
        $this->db->typeFixed();
    }

    public function sortSeller($sortOrder)
    {
        $this->db->join('users', 'items.user_id', '=', 'users.user_id');

        $this->db->sort('users.username', $sortOrder);
    }

    public function sortStartingIn($sortOrder)
    {
        $this->db->sort(DB::raw('cast(`start_date` as signed) - unix_timestamp()'), $sortOrder);
    }

    public function sortTimeLeft($sortOrder)
    {
        $this->db->sort(DB::raw('cast(`end_date` as signed) - unix_timestamp()'), $sortOrder);
    }
}
