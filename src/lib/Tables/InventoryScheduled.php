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

class InventoryScheduled extends Table
{
    /**
     * Column headers for the table. URL-friendly keys with human values.
     *
     * @var array
     */
    public $columnHeaders = [
        'Item ID'     => 'item_id',
        'Name'        => 'name',
        'Duration'    => 'duration',
        'Details'     => null,
        'Starting in' => 'starting_in',
        'Options'     => null,
    ];

    /**
     * Array of filter names containing available options and their keys.
     *
     * @var array
     */
    public $filters = [
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
    public $defaultSortKey = 'starting_in';

    /**
     * Class name for the paginator presenter.
     *
     * @var string
     */
    public $presenter = FoundationFivePresenter::class;

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
     * Sort by time until item starts.
     *
     * @return void
     */
    public function sortStartingIn($sortOrder)
    {
        $this->db->sort(DB::raw('cast(`start_date` as signed) - unix_timestamp()'), $sortOrder);
    }
}
