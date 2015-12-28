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

class InventoryWatching extends Table
{
    /**
     * Array of filter names containing available options and their keys.
     *
     * @var array
     */
    public $filters = [
        'Status' => [
            'Active',
            'Ended',
        ],
    ];

    /**
     * Default key to sort by.
     *
     * @var string
     */
    public $defaultSortKey = 'time_ended';

    /**
     * Class name for the paginator presenter.
     *
     * @var string
     */
    public $presenter = FoundationFivePresenter::class;

    /**
     * Include active items only.
     *
     * @return void
     */
    public function filterStatusActive()
    {
        $this->db->active();
    }

    /**
     * Include ended items only.
     *
     * @return void
     */
    public function filterStatusEnded()
    {
        $this->db->ended();
    }

    /**
     * Sort by time since item ended.
     *
     * @return void
     */
    public function sortTimeEnded($sortOrder)
    {
        $this->db->orderBy(DB::raw('unix_timestamp() - cast(`end_date` as signed)'), $sortOrder);
    }
}
