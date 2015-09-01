<?php

namespace Hamjoint\Mustard;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends NonSequentialIdModel implements  AuthenticatableContract, CanResetPasswordContract, HasRoleAndPermissionContract
{
    use Authenticatable, CanResetPassword, HasRoleAndPermission;

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
    protected $hidden = ['password_hash', 'remember_token'];

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->passwordHash;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->rememberToken;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->rememberToken = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

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
    public static function signUp($email, $password, $username)
    {
        $user = new self;

        $user->email = $email;
        $user->passwordHash = \Hash::make($password);
        $user->username = $username;
        $user->joined = time();
        $user->currency()->associate(Currency::first());
        $user->country()->associate(Country::first());

        $user->save();

        return $user;
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
