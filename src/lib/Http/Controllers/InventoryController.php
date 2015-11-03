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

use Auth;
use Hamjoint\Mustard\Tables\InventoryEnded;
use Hamjoint\Mustard\Tables\InventorySelling;
use Hamjoint\Mustard\Tables\InventoryScheduled;
use Hamjoint\Mustard\Tables\InventoryWatching;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Redirect index requests to the watching page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getIndex()
    {
        return mustard_redirect('/inventory/watching');
    }

    /**
     * Return the inventory watching view.
     *
     * @return \Illuminate\View\View
     */
    public function getWatching()
    {
        $table = new InventoryWatching(Auth::user()->watching());

        return view('mustard::inventory.watching', [
            'table' => $table,
            'items' => $table->paginate(),
        ]);
    }

    public function getSelling()
    {
        $items = Auth::user()->items()->active();

        $table = new InventorySelling($items);

        return view('mustard::inventory.selling', [
            'table' => $table,
            'items' => $table->paginate(),
        ]);
    }

    public function getScheduled()
    {
        $items = Auth::user()->items()->scheduled();

        $table = new InventoryScheduled($items);

        return view('mustard::inventory.scheduled', [
            'table' => $table,
            'items' => $table->paginate(),
        ]);
    }

    public function getEnded()
    {
        $items = Auth::user()->items()->where('end_date', '<', time());

        $table = new InventoryEnded($items);

        return view('mustard::inventory.ended', [
            'table' => $table,
            'items' => $table->paginate(),
        ]);
    }
}
