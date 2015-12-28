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
use Hamjoint\Mustard\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Redirect index requests to the password page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getIndex()
    {
        return mustard_redirect('/account/password');
    }

    /**
     * Return account password view.
     *
     * @return \Illuminate\View\View
     */
    public function getPassword()
    {
        return view('mustard::account.password', [
            'page' => 'password',
        ]);
    }

    /**
     * Change account password.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postPassword(Request $request)
    {
        $this->validates(
            $request->all(),
            [
                'old_password' => 'required',
                'new_password' => 'required|min:8',
            ]
        );

        if (!\Hash::check($request->get('old_password'), Auth::user()->password_hash)) {
            return redirect()->back()
                ->withErrors(['old_password' => 'Your old password is not correct.']);
        }

        if ($request->get('old_password') == $request->get('new_password')) {
            return redirect()->back();
        }

        Auth::user()->passwordHash = \Hash::make($request->get('new_password'));

        Auth::user()->save();

        Auth::user()->sendEmail(
            'Your password has been changed',
            'emails.account.password-changed'
        );

        return redirect()->back()
            ->with('message', 'Your password has been changed.');
    }

    /**
     * Return account email view.
     *
     * @return \Illuminate\View\View
     */
    public function getEmail()
    {
        return view('mustard::account.email', [
            'page' => 'email',
        ]);
    }

    /**
     * Change account email address.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEmail(Request $request)
    {
        $this->validates(
            $request->all(),
            [
                'email' => 'required|email',
            ]
        );

        if ($request->get('email') == Auth::user()->email) {
            return redirect()->back();
        }

        if (User::findByEmail($request->get('email'))) {
            return redirect()->back()
                ->withErrors(['email' => 'That email address is in use by another account.']);
        }

        Auth::user()->sendEmail(
            'Your email address has been changed',
            'emails.account.email-changed',
            ['new_email' => $request->get('email')]
        );

        Auth::user()->email = $request->get('email');

        Auth::user()->sendEmail(
            'Verify your new email address',
            'emails.account.verify',
            [
                'key'         => Auth::user()->addVerification(),
                'new_account' => false,
            ]
        );

        Auth::user()->verified = false;

        Auth::user()->save();

        return redirect()->back()
            ->with('message', 'Your email address has been changed.');
    }

    /**
     * Return account notifications view.
     *
     * @return \Illuminate\View\View
     */
    public function getNotifications()
    {
        return view('mustard::account.notifications', [
            'page' => 'notifications',
        ]);
    }

    /**
     * Change account notification settings.
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @todo Add logic
     */
    public function postNotifications()
    {
        return redirect()->back();
    }

    /**
     * Return account close view.
     *
     * @return \Illuminate\View\View
     */
    public function getClose()
    {
        return view('mustard::account.close');
    }
}
