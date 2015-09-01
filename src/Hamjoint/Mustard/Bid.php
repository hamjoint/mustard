<?php

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
