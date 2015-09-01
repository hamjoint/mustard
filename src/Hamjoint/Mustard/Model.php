<?php

namespace Hamjoint\Mustard;

class Model extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Mustard uses camel case for class properties, and snake case for database columns.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return parent::__get(snake_case($property));
    }

    /**
     * Mustard uses camel case for class properties, and snake case for database columns.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        parent::__set(snake_case($property), $value);
    }

    public function getUrlAttribute()
    {
        return '/' . strtolower(class_basename(static::class)) . '/' . $this->getKey() . '/' . $this->slug;
    }

    public function getSlugAttribute()
    {
        return str_slug($this->name);
    }
}
