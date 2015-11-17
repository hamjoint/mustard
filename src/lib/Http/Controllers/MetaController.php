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

use Hamjoint\Mustard\Item;

class MetaController extends Controller
{
    /**
     * Redirect index requests or return view.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getIndex()
    {
        $front_page = config('mustard.front_page', '/buy');

        return view()->exists(config('mustard.front_page'))
            ? view(config('mustard.front_page'))
            : mustard_redirect(config('mustard.front_page'));
    }
}
