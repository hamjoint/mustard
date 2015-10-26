<ul class="side-nav" role="navigation" title="Item categories">
    @foreach ($categories as $category)
        <li class="{{ !is_null($view_category) && $category->category_id == $view_category->category_id ? 'active' : '' }}" role="menuitem"><a href="{{ $category->url }}">{{ e($category->name) }}<?php /* <span>({{ mustard_number($category->activeItems()->count()) }})</span>*/ ?></a></li>
        @if (!$category->children->isEmpty())
            <li role="menuitem">
                <ul role="navigation" title="Item sub-categories">
                    @foreach ($category->children as $child)
                        <li class="{{ !is_null($view_category) && $child->category_id == $view_category->category_id ? 'active' : '' }}" role="menuitem"><a href="{{ $child->url }}">{{ e($child->name) }}<?php /* <span>({{ mustard_number($child->activeItems()->count()) }})</span>*/ ?></a></li>
                        @if (!$child->children->isEmpty())
                            <li role="menuitem">
                                <ul role="navigation" title="Item sub-categories">
                                    @foreach ($child->children as $grand_child)
                                        <li class="{{ !is_null($view_category) && $grand_child->category_id == $view_category->category_id ? 'active' : '' }}" role="menuitem"><a href="{{ $grand_child->url }}">{{ e($grand_child->name) }}<?php /* <span>({{ mustard_number($grand_child->activeItems()->count()) }})</span>*/ ?></a></li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>
        @endif
    @endforeach
</ul>
