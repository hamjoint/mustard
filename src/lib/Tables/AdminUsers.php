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

class AdminUsers extends Table
{
    public $columnHeaders = [
        'User ID' => 'user_id',
        'Username' => 'username',
        'Email' => 'email',
        'Joined' => 'joined',
        'Last login' => 'last_login',
    ];

    public $filters = [
        'Type' => [
            'Buyer',
            'Seller',
        ],
    ];

    public $eagerLoad = [
        'feedbackReceived',
    ];

    public $defaultSortKey = 'username';

    public $presenter = FoundationFivePresenter::class;

    public function filterTypeBuyer($builder)
    {
        $builder->buyers();
    }

    public function filterTypeSeller($builder)
    {
        $builder->sellers();
    }
}
