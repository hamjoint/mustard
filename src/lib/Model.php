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

use EloquentJoins\ModelTrait as EloquentJoinsModelTrait;

abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    use EloquentJoinsModelTrait;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Mustard uses camel case for class properties, and snake case for
     * database columns.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return parent::__get(snake_case($property));
    }

    /**
     * Get a relationship. We override this method from the parent to add a
     * camel_case() conversion for the key.
     */
    public function getRelationValue($key)
    {
        $key = camel_case($key);

        // If the key already exists in the relationships array, it just means the
        // relationship has already been loaded, so we'll just return it out of
        // here because there is no need to query within the relations twice.
        if ($this->relationLoaded($key)) {
            return $this->relations[$key];
        }

        // If the "attribute" exists as a method on the model, we will just assume
        // it is a relationship and will load and return results from the query
        // and hydrate the relationship's value on the "relationships" array.
        if (method_exists($this, $key)) {
            return $this->getRelationshipFromMethod($key);
        }
    }

    /**
     * Mustard uses camel case for class properties, and snake case for database columns.
     *
     * @param string $property
     * @param mixed  $value
     */
    public function __set($property, $value)
    {
        parent::__set(snake_case($property), $value);
    }

    public function getUrlAttribute()
    {
        return '/'.strtolower(class_basename(static::class)).'/'.$this->getKey().'/'.$this->slug;
    }

    public function getSlugAttribute()
    {
        return str_slug($this->name);
    }
}
