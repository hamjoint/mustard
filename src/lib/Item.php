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

class Item extends NonSequentialIdModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'items';

    /**
     * The database key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'item_id';

    /**
     * Return UNIX timestamp for item's end.
     *
     * @return integer
     */
    public function getEndDate()
    {
        return $this->startDate + $this->duration;
    }

    /**
     * Return the time difference between now and the end of the item.
     *
     * @return \DateInterval
     */
    public function getTimeLeft()
    {
        return (new DateTime())->diff(DateTime::createFromFormat('U', $this->endDate));
    }

    /**
     * Return the time difference between now and the start of the item.
     *
     * @return \DateInterval
     */
    public function getStartingIn()
    {
        return (new DateTime())->diff(DateTime::createFromFormat('U', $this->startDate));
    }

    /**
     * Return the time difference between 0 and the end of the duration.
     *
     * @return \DateInterval
     */
    public function getDuration()
    {
        return DateTime::createFromFormat('U', 0)->diff(DateTime::createFromFormat('U', $this->duration));
    }

    /**
     * Return the highest bid amount placed.
     *
     * @return float
     */
    public function getHighestBidAmount()
    {
        if ($this->isFixed()) return null;

        return $this->bids->max('amount') ?: $this->startPrice;
    }

    /**
     * Return the primary photo.
     *
     * @return Photo
     */
    public function getListingPhoto()
    {
        return $this->photos->filter(function($photo)
        {
            return $photo->primary;
        })->first() ?: new \Hamjoint\Mustard\Media\Photo;
    }

    /**
     * Return the item's bid history.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBidHistory()
    {
        return $this->bids()
            ->orderBy('amount', 'desc')
            ->orderBy('placed', 'asc')
            ->get();
    }

    /**
     * Return true if item can be collected by the buyer.
     *
     * @return boolean
     */
    public function isCollectable()
    {
        return (bool) $this->collectionLocation;
    }

    /**
     * Return true if user has bid on the item.
     *
     * @param \Hamjoint\Mustard\User $user
     * @return boolean
     */
    public function isBidder(User $user)
    {
        return (bool) $this->bids()->whereHas('bidder', function($query) use ($user)
        {
            return $query->where('user_id', $user->userId);
        })->count();
    }

    /**
     * Return true if user has won the item.
     *
     * @param \Hamjoint\Mustard\User $user
     * @return boolean
     */
    public function isWinner(User $user)
    {
        if (!$this->auction || !$this->isEnded()) return false;

        return (bool) ($this->winningBid && $this->winningBid->bidder == $user);
    }

    /**
     * Return true if user has ever purchased the item.
     *
     * @param \Hamjoint\Mustard\User $user
     * @return boolean
     */
    public function isBuyer(User $user)
    {
        return (bool) $this->purchases()->whereHas('buyer', function($query) use ($user)
        {
            return $query->where('user_id', $user->userId);
        })->count();
    }

    /**
     * Return true if the item is still active.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->endDate >= time() && $this->startDate <= time();
    }

    /**
     * Return true if the item has started.
     *
     * @return boolean
     */
    public function isStarted()
    {
        return $this->startDate < time();
    }

    /**
     * Return true if the item has ended.
     *
     * @return boolean
     */
    public function isEnded()
    {
        return $this->endDate < time();
    }

    /**
     * Return true if the item is has not reached a reserve price.
     *
     * @return boolean
     */
    public function isReserved()
    {
        return $this->reservePrice > 0 && $this->reservePrice > $this->biddingPrice;
    }

    /**
     * Return true if the item has a fixed price.
     *
     * @return boolean
     */
    public function hasFixed()
    {
        return $this->fixedPrice > 0;
    }

    /**
     * Return true if the item has a quantity.
     *
     * @return boolean
     */
    public function hasQuantity()
    {
        return !$this->auction && $this->quantity > 1;
    }

    /**
     * Return true if the item has a reserve price.
     *
     * @return boolean
     */
    public function hasReserve()
    {
        return $this->reservePrice > 0;
    }

    /**
     * Return true if the item has bids.
     *
     * @return boolean
     */
    public function hasBids()
    {
        return (bool) $this->bids->count();
    }

    /**
     * Shortcut method for placing a bid.
     *
     * @param float $amount
     * @param \Hamjoint\Mustard\User $user
     * @return void
     */
    public function placeBid($amount, User $user)
    {
        $bid = new \Hamjoint\Mustard\Auctions\Bid;

        $bid->amount = $amount;
        $bid->placed = time();
        $bid->bidder()->associate($user);
        $bid->item()->associate($this);

        $bid->save();
    }

    /**
     * End an item, marking a winning bid.
     *
     * @return void
     */
    public function end()
    {
        if (mustard_loaded('auctions') && $this->auction) {
            $bid = $this->getBidHistory()->first();

            if ($bid) $this->winningBid()->associate($bid);
        }

        $now = time();

        if ($this->endDate > $now) {
            $this->endedEarly = true;

            $this->endDate = $now;
        }

        $this->save();
    }

    /**
     * Withdraw an item without marking a winning bid.
     *
     * @return void
     */
    public function withdraw()
    {
        $this->withdrawn = $this->endedEarly = true;

        $now = time();

        if ($this->endDate > $now) $this->endDate = $now;

        $this->save();
    }

    /**
     * Search the name and description of items for specific keywords.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $keyword
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKeywords($query, $keyword)
    {
        return $query->where(function ($query) use ($keyword) {
            $query->where('name', 'LIKE', "%$keyword%")
            ->orWhere('description', 'LIKE', "%$keyword%");
        });
    }

    /**
     * Scope of active items.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', time())
            ->where('end_date', '>=', time());
    }

    /**
     * Scope of ended items.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnded($query)
    {
        return $query->where('end_date', '<', time());
    }

    /**
     * Scope of scheduled items.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeScheduled($query)
    {
        return $query->where('start_date', '>', time());
    }

    /**
     * Scope of auction items.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTypeAuction($query)
    {
        return $query->where('auction', true);
    }

    /**
     * Scope of fixed-price items.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTypeFixed($query)
    {
        return $query->where('auction', false);
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
     * Relationship to categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany('\Hamjoint\Mustard\Category', 'item_categories');
    }

    /**
     * Relationship to an item condition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function condition()
    {
        return $this->belongsTo('\Hamjoint\Mustard\ItemCondition', 'item_condition_id');
    }

    /**
     * Relationship to delivery options.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deliveryOptions()
    {
        return $this->hasMany('\Hamjoint\Mustard\DeliveryOption');
    }

    /**
     * Relationship to photos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany('\Hamjoint\Mustard\Media\Photo');
    }

    /**
     * Relationship to purchases.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchases()
    {
        return $this->hasMany('\Hamjoint\Mustard\Commerce\Purchase');
    }

    /**
     * Relationship to the user selling the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo('\Hamjoint\Mustard\User', 'user_id');
    }

    /**
     * Relationship to the users watching the item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function watchers()
    {
        return $this->belongsToMany('\Hamjoint\Mustard\User', 'watched_items');
    }

    /**
     * Relationship to the winning bid.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function winningBid()
    {
        return $this->belongsTo('\Hamjoint\Mustard\Auctions\Bid', 'winning_bid_id');
    }

    /**
     * Return the minimum possible bid, respecting configured bid increments.
     *
     * @param float $currentAmount
     * @return float
     */
    public static function getMinimumBidAmount($currentAmount)
    {
        $bid_increments = \Hamjoint\Mustard\Auctions\BidIncrement::orderBy('increment', 'asc')->get();

        foreach ($bid_increments as $bid_increment) {
            if ($currentAmount < $bid_increment->increment) {
                return $currentAmount + $bid_increment->increment;
            }
        }

        return $currentAmount + $bid_increments->max('increment');
    }

    /**
     * Return the total number of items.
     *
     * @param integer $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param integer $until UNIX timestamp to optionally specify an upper selection boundary.
     * @return integer
     */
    public static function totalListed($since = 0, $until = null)
    {
        $until = $until ?: time();

        return (int) self::where('created', '>=', $since)
            ->where('created', '<=', $until)
            ->count();
    }

    /**
     * Return the total number of watched items.
     *
     * @param integer $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param integer $until UNIX timestamp to optionally specify an upper selection boundary.
     * @return integer
     */
    public static function totalWatched($since = 0, $until = null)
    {
        $until = $until ?: time();

        return (int) self::whereHas('watchers', function ($query) use ($since, $until) {
            $query->where('added', '>=', $since);
            $query->where('added', '<=', $until);
        })->count();
    }
}
