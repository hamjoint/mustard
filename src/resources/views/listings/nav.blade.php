<ul class="{{ $root ? 'side-nav' : '' }} no-bullet" role="navigation" title="Item categories">
    @foreach ($categories as $category)
        <li class="{{ !is_null($view_category) && $category->getKey() == $view_category->getKey() ? 'active' : '' }}" role="menuitem">
            <a href="{{ $category->url }}">
                {{ e($category->name) }}
                <span>({{ mustard_number($category->getItemCount()) }})</span>
            </a>
        </li>
        @if (!$category->children->isEmpty())
            <li role="menuitem">
                @include('mustard::listings.nav', ['categories' => $category->children, 'root' => false])
            </li>
        @endif
    @endforeach
</ul>
