<?php

namespace Hamjoint\Mustard;

class NonSequentialIdModel extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Find and use an unused non-sequential ID for a record, as part of a
     * model's creation. ID will be a 32-bit unsigned integer of 10 characters
     * in length.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::creating(function($model)
        {
            do {
                $id = mt_rand(pow(10, 9), pow(2, 32) - 1);
            } while (self::find($id));

            $model->{$model->getKeyName()} = $id;
        });
    }
}
