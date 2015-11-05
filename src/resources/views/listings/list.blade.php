@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    @if (!is_null($view_category))
        @foreach ($view_category->getAncestors() as $category)
            {{ e($category->name) }} -
        @endforeach
        {{ e($view_category->name) }}
    @else
        All Categories
    @endif
@stop

@section('content')
<div class="row listings-breadcrumbs">
    <div class="medium-12 columns">
        @include('mustard::listings.breadcrumbs')
    </div>
</div>
<div class="row">
    <div class="medium-3 columns">
        @include('mustard::listings.nav')
    </div>
    <div class="medium-9 columns">
        @include('tablelegs::filter')
        @if (!$table->isEmpty())
            @foreach ($items as $item)
                @include('mustard::listings.item')
            @endforeach
        @else
            <p>We don't have any active items in this category. <a href="/sell">Sell</a> one now!</p>
        @endif
        <div class="row">
            <div class="medium-12 columns pagination-centered">
                {!! $table->paginator() !!}
            </div>
        </div>
    </div>
</div>
@stop
