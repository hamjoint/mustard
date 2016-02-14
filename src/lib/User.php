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

use DateTime;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Collection;
use Mail;

class User extends NonSequentialIdModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
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
     * @param array  $vars
     */
    public function sendEmail($subject, $view, $vars = [])
    {
        $vars['email'] = $email = $this->email;
        $vars['username'] = $username = $this->username;

        Mail::queue(['text' => $view], $vars, function ($message) use ($email, $username, $subject) {
            $message->subject($subject)->to($email, $username);

            if (config()->has('mail.reply_to')) {
                $message->replyTo(
                    config('mail.reply_to.email'),
                    config('mail.reply_to.name')
                );
            }
        });
    }

    /**
     * Return the user's feedback score.
     *
     * @return int
     */
    public function getFeedbackScore()
    {
        return $this->feedbackReceived->sum('rating');
    }

    /**
     * Get users the user has a substantial connection with.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAcquaintances()
    {
        $acquaintances = new Collection();

        // Past message senders
        foreach ($this->messages()->received()->with('sender')->get() as $message) {
            $acquaintances = $acquaintances->push($message->sender);
        }

        // Past message recipients
        foreach ($this->messages()->sent()->with('recipient')->get() as $message) {
            $acquaintances = $acquaintances->push($message->recipient);
        }

        // Past sellers
        foreach ($this->purchases()->with('item.seller')->get() as $purchase) {
            $acquaintances = $acquaintances->push($purchase->item->seller);
        }

        // Past buyers
        foreach ($this->items()->with('purchases.buyer')->get() as $item) {
            foreach ($item->purchases as $purchase) {
                $acquaintances = $acquaintances->push($purchase->buyer);
            }
        }

        return $acquaintances->unique();
    }

    /**
     * Return the time difference between now and the user's last login.
     *
     * @return \DateInterval
     */
    public function getSinceLastLogin()
    {
        $last_login = DateTime::createFromFormat('U', $this->lastLogin);

        return $last_login->diff(new DateTime());
    }

    /**
     * Return the time difference between now and the user's join date.
     *
     * @return \DateInterval
     */
    public function getSinceJoined()
    {
        $joined = DateTime::createFromFormat('U', $this->joined);

        return $joined->diff(new DateTime());
    }

    /**
     * Override the parent's url attribute.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return '/'.strtolower(class_basename(static::class)).'/'.$this->getKey();
    }

    /**
     * Return the user's feedback url.
     *
     * @return string
     */
    public function getFeedbackUrlAttribute()
    {
        return '/'.strtolower(class_basename(static::class)).'/feedback/'.$this->getKey();
    }

    /**
     * Return true if user is watching the item.
     *
     * @param \Hamjoint\Mustard\Item $item
     *
     * @return bool
     */
    public function isWatching(Item $item)
    {
        return (bool) $this->watching()->where('items.item_id', $item->itemId)->count();
    }

    /**
     * Return number of unread messages.
     *
     * @return int
     */
    public function getUnreadMessages()
    {
        return $this->messages()->received()->unread()->count();
    }

    /**
     * Scope of buyers.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuyers($query)
    {
        return $query->has('purchases');
    }

    /**
     * Scope of sellers.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSellers($query)
    {
        return $query->has('items');
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
        return $this->hasMany('\Hamjoint\Mustard\Feedback\UserFeedback', 'rater_user_id');
    }

    /**
     * Relationship to feedback received from other users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function feedbackReceived()
    {
        return $this->hasMany('\Hamjoint\Mustard\Feedback\UserFeedback', 'subject_user_id');
    }

    /**
     * Relationship to items the user has listed.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany('\Hamjoint\Mustard\Item');
    }

    /**
     * Relationship to messages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany('\Hamjoint\Mustard\Messaging\Message');
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
     *
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
     *
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
     *
     * @return \Hamjoint\Mustard\User
     */
    public static function register($email, $password, $username)
    {
        $user = new self();

        $user->email = $email;

        $user->password = bcrypt($password);

        $user->username = $username;

        $user->joined = time();

        $user->save();

        return $user;
    }

    /**
     * Return the total number of users.
     *
     * @param int $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param int $until UNIX timestamp to optionally specify an upper selection boundary.
     *
     * @return int
     */
    public static function totalRegistered($since = 0, $until = null)
    {
        $until = $until ?: time();

        return (int) self::where('joined', '>=', $since)
            ->where('joined', '<=', $until)
            ->count();
    }

    /**
     * Return the total number of users that have placed bids.
     *
     * @param int $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param int $until UNIX timestamp to optionally specify an upper selection boundary.
     *
     * @return int
     */
    public static function totalBidders($since = 0, $until = null)
    {
        $until = $until ?: time();

        return (int) self::join('bids')
            ->where('placed', '>=', $since)
            ->where('placed', '<=', $until)
            ->count(\DB::raw('DISTINCT(users.user_id)'));
    }

    /**
     * Return the total number of users with associated purchases.
     *
     * @param int $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param int $until UNIX timestamp to optionally specify an upper selection boundary.
     *
     * @return int
     */
    public static function totalBuyers($since = 0, $until = null)
    {
        $until = $until ?: time();

        return (int) self::join('purchases')
            ->where('created', '>=', $since)
            ->where('created', '<=', $until)
            ->count(\DB::raw('DISTINCT(users.user_id)'));
    }

    /**
     * Return the total number of users that have posted items.
     *
     * @param int $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param int $until UNIX timestamp to optionally specify an upper selection boundary.
     *
     * @return int
     */
    public static function totalSellers($since = 0, $until = null)
    {
        $until = $until ?: time();

        return (int) self::join('items')
            ->where('created', '>=', $since)
            ->where('created', '<=', $until)
            ->count(\DB::raw('DISTINCT(users.user_id)'));
    }
}
