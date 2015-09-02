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

namespace Hamjoint\Mustard;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends NonSequentialIdModel implements  AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The database key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Shortcut method for sending a user an email.
     *
     * @param string $subject
     * @param string $view
     * @param array $vars
     */
    public function sendEmail($subject, $view, $vars = [])
    {
        $vars['email'] = $email = $this->email;
        $vars['handle'] = $handle = $this->getHandle();

        \Mail::queue(['text' => $view], $vars, function($message) use ($email, $handle, $subject)
        {
            $message->subject($subject)
                ->to($email, $handle)
                ->replyTo(
                    \Config::get('mail.reply_to.address'),
                    \Config::get('mail.reply_to.name')
                );
        });
    }

    /**
     * Return the user's feedback score.
     *
     * @return integer
     */
    public function getFeedbackScore()
    {
        return $this->feedbackReceived()->sum('modifier');
    }

    /**
     * Override the parent's url attribute
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return '/' . strtolower(class_basename(static::class)) . '/' . $this->getKey();
    }

    /**
     * Relationship to bank details.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bankDetails()
    {
        return $this->hasOne('\Hamjoint\Mustard\Commerce\BankDetail');
    }

    /**
     * Relationship to bids.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bids()
    {
        return $this->hasMany('\Hamjoint\Mustard\Auctions\Bid');
    }

    /**
     * Relationship to feedback left for other users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedbackLeft()
    {
        return $this->hasMany('\Hamjoint\Mustard\Feedback\UserFeedback');
    }

    /**
     * Relationship to feedback received from other users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function feedbackReceived()
    {
        return $this->hasManyThrough('\Hamjoint\Mustard\Feedback\UserFeedback', '\Hamjoint\Mustard\Item');
    }

    /**
     * Relationship to postal addresses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postalAddresses()
    {
        return $this->hasMany('\Hamjoint\Mustard\Commerce\PostalAddress');
    }

    /**
     * Relationship to item purchases.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchases()
    {
        return $this->hasMany('\Hamjoint\Mustard\Commerce\Purchase');
    }

    /**
     * Relationship to sold items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function sales()
    {
        return $this->hasManyThrough('\Hamjoint\Mustard\Commerce\Purchase', '\Hamjoint\Mustard\Item');
    }

    /**
     * Relationship to items the user is selling.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function selling()
    {
        return $this->hasMany('\Hamjoint\Mustard\Item');
    }

    /**
     * Relationship to items the user is watching.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function watching()
    {
        return $this->belongsToMany('\Hamjoint\Mustard\Item', 'watched_items');
    }
    /**
     * Relationship to won items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function won()
    {
        return $this->hasManyThrough('\Hamjoint\Mustard\Item', '\Hamjoint\Mustard\Auctions\Bid', 'user_id', 'winning_bid_id');
    }

    /**
     * Find a record by the email address.
     *
     * @param string $email
     * @return \Hamjoint\Mustard\User|null
     */
    public static function findByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    /**
     * Find a record by the email address.
     *
     * @param string $username
     * @return \Hamjoint\Mustard\User|null
     */
    public static function findByUsername($username)
    {
        return self::where('username', 'LIKE', $username)->first();
    }

    /**
     * Shortcut method for signing up a new user.
     *
     * @param string $email
     * @param string $password
     * @param string $username
     * @return \Hamjoint\Mustard\User
     */
    public static function register($email, $password, $username)
    {
        return self::create([
            'email' => $email,
            'passwordHash' => bcrypt($password),
            'username' => $username,
            'joined' => time(),
        ]);
    }

    /**
     * Return the total number of users.
     *
     * @param integer $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param integer $until UNIX timestamp to optionally specify an upper selection boundary.
     * @return integer
     */
    public static function totalRegistered($since = 0, $until = null)
    {
        $until = $until ?: time();

        return self::where('joined', '>=', $since)
            ->where('joined', '<=', $until)
            ->count();
    }

    /**
     * Return the total number of users that have placed bids.
     *
     * @param integer $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param integer $until UNIX timestamp to optionally specify an upper selection boundary.
     * @return integer
     */
    public static function totalBidders($since = 0, $until = null)
    {
        $until = $until ?: time();

        return self::join('bids', 'bids.user_id', '=', 'users.user_id')
            ->where('placed', '>=', $since)
            ->where('placed', '<=', $until)
            ->count(\DB::raw('DISTINCT(users.user_id)'));
    }

    /**
     * Return the total number of users with associated purchases.
     *
     * @param integer $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param integer $until UNIX timestamp to optionally specify an upper selection boundary.
     * @return integer
     */
    public static function totalBuyers($since = 0, $until = null)
    {
        $until = $until ?: time();

        return self::join('purchases', 'purchases.user_id', '=', 'users.user_id')
            ->where('created', '>=', $since)
            ->where('created', '<=', $until)
            ->count(\DB::raw('DISTINCT(users.user_id)'));
    }

    /**
     * Return the total number of users that have posted items.
     *
     * @param integer $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param integer $until UNIX timestamp to optionally specify an upper selection boundary.
     * @return integer
     */
    public static function totalSellers($since = 0, $until = null)
    {
        $until = $until ?: time();

        return self::join('items', 'items.user_id', '=', 'users.user_id')
            ->where('created', '>=', $since)
            ->where('created', '<=', $until)
            ->count(\DB::raw('DISTINCT(users.user_id)'));
    }
}
