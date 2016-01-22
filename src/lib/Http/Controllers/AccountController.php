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
use Hash;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Redirect index requests to the password page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return mustard_redirect('/account/password');
    }

    /**
     * Return account password view.
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
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
    public function changePassword(Request $request)
    {
        $this->validate(
            $request,
            [
                'old_password' => 'required',
                'new_password' => 'required|min:8',
            ]
        );

        if (!Hash::check($request->input('old_password'), Auth::user()->password)) {
            return redirect()->back()->withErrors([
                'old_password' => trans('mustard::account.password_incorrect')
            ]);
        }

        if ($request->input('old_password') == $request->input('new_password')) {
            return redirect()->back()->withErrors([
                'new_password' => trans('mustard::account.password_same')
            ]);
        }

        Auth::user()->password = Hash::make($request->input('new_password'));

        Auth::user()->save();

        Auth::user()->sendEmail(
            trans('mustard::account.password_changed'),
            'mustard::emails.account.password-changed'
        );

        return redirect()->back()->withStatus(trans('mustard::account.password_changed'));
    }

    /**
     * Return account email view.
     *
     * @return \Illuminate\View\View
     */
    public function showChangeEmailForm()
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
    public function changeEmail(Request $request)
    {
        $this->validate(
            $request,
            [
                'email' => 'required|email',
            ]
        );

        if ($request->get('email') == Auth::user()->email) {
            return redirect()->back()->withErrors([
                'email' => trans('mustard::account.email_same')
            ]);
        }

        if (User::findByEmail($request->get('email'))) {
            return redirect()->back()->withErrors([
                'email' => trans('mustard::account.email_exists')
            ]);
        }

        // Notify the old email
        Auth::user()->sendEmail(
            trans('mustard::account.email_changed'),
            'mustard::emails.account.email-changed',
            ['new_email' => $request->get('email')]
        );

        Auth::user()->email = $request->get('email');

        if (method_exists('unverify', Auth::user())) {
            Auth::user()->unverify();
        }

        Auth::user()->save();

        return redirect()->back()->withStatus(trans('mustard::account.email_changed'));
    }

    /**
     * Return account notifications view.
     *
     * @return \Illuminate\View\View
     */
    public function showChangeNotificationsForm()
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
    public function changeNotifications()
    {
        return redirect()->back();
    }

    /**
     * Return account close view.
     *
     * @return \Illuminate\View\View
     */
    public function showCloseAccountForm()
    {
        return view('mustard::account.close');
    }

    /**
     * Close an account.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function closeAccount(Request $request)
    {
        $this->validate(
            $request,
            [
                'confirm' => 'required|regex:/close my account/',
            ]
        );

        Auth::user()->watching()->detach();

        if (mustard_loaded('commerce')) {
            Auth::user()->bankDetails()->delete();
        }

        if (mustard_loaded('feedback')) {
            Auth::user()->feedbackReceived()->update(['subject_user_id' => null]);
        }

        if (mustard_loaded('messaging')) {
            Auth::user()->messages()->delete();
        }

        return redirect()->back()->withStatus(trans('mustard::account.close_confirmed'));
    }
}
