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

class Bid extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bids';

    /**
     * The database key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'bid_id';

    /**
     * Relationship to an item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo('\Models\Item');
    }

    /**
     * Relationship to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bidder()
    {
        return $this->belongsTo('\Models\User', 'user_id');
    }

    /**
     * Return the total number of bids.
     *
     * @param integer $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param integer $until UNIX timestamp to optionally specify an upper selection boundary.
     * @return integer
     */
    public static function totalPlaced($since = 0, $until = null)
    {
        $until = $until ?: time();

        return self::where('placed', '>=', $since)
            ->where('placed', '<=', $until)
            ->count();
    }

    /**
     * Return the average amount of bids.
     *
     * @param integer $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param integer $until UNIX timestamp to optionally specify an upper selection boundary.
     * @return integer
     */
    public static function averageAmount($since = 0, $until = null)
    {
        $until = $until ?: time();

        return self::where('placed', '>=', $since)
            ->where('placed', '<=', $until)
            ->avg('amount');
    }
}
