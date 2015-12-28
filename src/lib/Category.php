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

use Cache;
use Illuminate\Support\Collection;

class Category extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The database key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'category_id';

    /**
     * Override the parent's url attribute.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        $url = '';

        foreach (array_reverse($this->getAncestors()->all()) as $parent) {
            $url = '/'.$parent->slug.$url;
        }

        return "/buy$url/".$this->slug;
    }

    /**
     * Scope of categories with no parents.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_category_id');
    }

    /**
     * Scope of categories with no children.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLeaves($query)
    {
        return $query->has('children', 0);
    }

    /**
     * Recursively build collection of ancestor category IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAncestors()
    {
        $ancestors = new Collection();

        $ancestor = $this;

        while (($ancestor = $ancestor->parent) && !$ancestors->contains($ancestor)) {
            $ancestors->push($ancestor);
            break;
        }

        return $ancestors;
    }

    /**
     * Recursively build collection of descendant category IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDescendants()
    {
        $descendants = $this->children;

        foreach ($this->children as $child) {
            $descendants = $descendants->merge($child->getDescendants());
        }

        return $descendants;
    }

    /**
     * Return array of descendant category IDs.
     *
     * @return array
     */
    public function getDescendantIds()
    {
        return array_pluck($this->getDescendants(), 'category_id');
    }

    public function getItemCount()
    {
        return Cache::remember('category_item_count_'.$this->getKey(), 1, function () {
            $count = $this->items()->active()->count();

            $this->getDescendants()->each(function ($descendant) use (&$count) {
                $count += $descendant->items()->active()->count();
            });

            return $count;
        });
    }

    /**
     * Relationship to child categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany('\Hamjoint\Mustard\Category', 'parent_category_id');
    }

    /**
     * Relationship to items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function items()
    {
        return $this->belongsToMany('\Hamjoint\Mustard\Item', 'item_categories');
    }

    /**
     * Relationship to a parent category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('\Hamjoint\Mustard\Category', 'parent_category_id');
    }

    /**
     * Find a record by the slug.
     *
     * @param string $slug
     *
     * @return \Hamjoint\Mustard\Category|null
     */
    public static function findBySlug($slug)
    {
        return self::where('slug', $slug)->first();
    }
}
