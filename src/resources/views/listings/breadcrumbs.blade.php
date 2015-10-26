<nav class="breadcrumbs">
    <a href="/buy">Buy</a>
    @if (!is_null($view_category))
        @foreach ($view_category->getAncestors() as $category)
        <a href="{{ $category->url }}">{{ e($category->name) }}</a>
        @endforeach
        <a class="current" href="{{ $view_category->url }}">{{ e($view_category->name) }}</a>
    @else
        <a class="current" href="/buy">All Categories</a>
    @endif
</nav>
