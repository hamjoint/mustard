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

use Foundation\Pagination\FoundationFivePresenter;
use Tablelegs\Table;

class ListingItems extends Table
{
    /**
     * Array of filter names containing available options and their keys.
     *
     * @var array
     */
    public $filters = [
        'Has' => [
            'No bids',
            'Fixed price',
        ],
    ];

    /**
     * Default key to sort by.
     *
     * @var string
     */
    public $defaultSortKey = 'end_date';

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
    public function filterHasNoBids()
    {
        $this->db->TypeAuction()->has('bids', 0);
    }

    /**
     * Include fixed-type items only.
     *
     * @return void
     */
    public function filterHasFixedPrice()
    {
        $this->db->where('fixed_price', '>', 0);
    }
}
