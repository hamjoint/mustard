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

use Hamjoint\Mustard\Category;
use Hamjoint\Mustard\Item;
use Hamjoint\Mustard\Tables\ListingItems;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    /**
     * Return the item listings view.
     *
     * @return \Illuminate\View\View
     */
    public function getIndex(Request $request, $path = null)
    {
        $items = Item::active();

        if ($request->input('q')) {
            $items->keywords($request->input('q'));
        }

        // Handle root
        if (is_null($path)) {
            $category = null;
        } else {
            $tree = explode('/', $path);

            $category = Category::findBySlug(array_slice($tree, -1)[0]);

            $items->whereHas('categories', function ($query) use ($category) {
                $query->whereIn(
                    'categories.category_id',
                    array_merge($category->getDescendantIds(), [$category->getKey()])
                );
            });
        }

        $table = new ListingItems($items, $request);

        $table->with('categories');

        if (mustard_loaded('auctions')) {
            $table->with('bids');
        }

        if (mustard_loaded('media')) {
            $table->with('photos');
        }

        return view('mustard::listings.list', [
            'categories' => Category::roots()
                ->with('children')
                ->orderBy('sort', 'asc')
                ->orderBy('name', 'asc')
                ->get(),
            'table'         => $table,
            'items'         => $table->paginate(),
            'view_category' => $category,
        ]);
    }
}
