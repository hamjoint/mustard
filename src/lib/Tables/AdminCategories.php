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

class AdminCategories extends Table
{
    /**
     * Column headers for the table. URL-friendly keys with human values.
     *
     * @var array
     */
    public $columnHeaders = [
        'Category ID' => 'category_id',
        'Parent' => 'parent_category_id',
        'Name' => 'name',
        'Slug' => 'slug',
        'Items' => 'items',
    ];

    /**
     * Array of filter names containing available options and their keys.
     *
     * @var array
     */
    public $filters = [
        'Type' => [
            'Root',
            'Leaf',
        ],
    ];

    /**
     * Default key to sort by.
     *
     * @var string
     */
    public $defaultSortKey = 'sort';

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
    public function filterTypeRoot()
    {
        $this->db->roots();
    }

    /**
     * Include fixed-type items only.
     *
     * @return void
     */
    public function filterTypeLeaf()
    {
        $this->db->leaves();
    }

    /**
     * Sort by category ID.
     *
     * @return void
     */
    public function sortCategoryId($sortOrder)
    {
        $this->db->sort('categories.category_id', $sortOrder);
    }

    /**
     * Sort by total items.
     *
     * @return void
     */
    public function sortItems($sortOrder)
    {
        $this->db->sort('item_count', $sortOrder);
    }
}
