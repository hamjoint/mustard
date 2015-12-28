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

use Hamjoint\Mustard\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UserController extends Controller
{
    /**
     * Return the user profile view.
     *
     * @return \Illuminate\View\View
     */
    public function getIndex(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $items = $user->items()
            ->where('start_date', '<=', time())
            ->where('end_date', '>=', time())
            ->orderBy('end_date', 'asc')
            ->paginate();

        return view('mustard::user.profile', [
            'user'      => $user,
            'items'     => $items,
            'feedbacks' => mustard_loaded('feedback')
                ? $user->feedbackReceived()->orderBy('placed')->take(3)->get()
                : new Collection(),
        ]);
    }
}
