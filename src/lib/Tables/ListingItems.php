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
    public $filters = [
        'Type' => [
            'Auction',
            'Fixed'
        ],
    ];

    public $eagerLoad = [
        'bids',
        'categories',
        'photos',
    ];

    public $defaultSortKey = 'end_date';

    public $presenter = FoundationFivePresenter::class;

    public function filterTypeAuction($builder)
    {
        $builder->TypeAuction();
    }

    public function filterTypeFixed($builder)
    {
        $builder->TypeFixed();
    }
}
