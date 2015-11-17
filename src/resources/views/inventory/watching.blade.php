@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Watching - Inventory
@stop

@section('content')
<div class="row">
    <div class="medium-2 columns">
        @include('mustard::inventory.nav')
    </div>
    <div class="medium-10 columns">
        @include('tablelegs::filter')
        @if (!$table->isEmpty())
            @foreach ($items->chunk(4) as $chunked_items)
                <div class="row">
                    @foreach ($chunked_items as $item)
                        <div class="medium-3 columns end mosaic {{ $item->isEnded() ? 'ended' : '' }}">
                            <div class="image">
                                <a href="{{ $item->url }}">
                                    @if (mustard_loaded('media'))
                                        <img src="{{ $item->getListingPhoto()->urlSmall }}" alt="" />
                                    @endif
                                </a>
                                <form method="post" action="/item/unwatch">
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="item_id" value="{{ $item->itemId }}" />
                                    <input class="button radius tiny alert" type="submit" value="Remove" />
                                </form>
                                <div class="price">
                                    @if (mustard_loaded('auctions') && $item->auction)
                                        {{ mustard_price($item->biddingPrice, true) }} ({{ mustard_number($item->bids->count(), 0) }} bids)
                                        @if ($item->hasFixed())
                                            <br />or buy now for {{ mustard_price($item->fixedPrice ?: '-', true) }}
                                        @endif
                                    @else
                                        {{ mustard_price($item->fixedPrice ?: '-', true) }}
                                    @endif
                                </div>
                                @if (!$item->isEnded())
                                    <div class="time-left">
                                        {{ mustard_time($item->getTimeLeft(), 2, true) }} left
                                    </div>
                                @endif
                            </div>
                            <div><strong><a href="{{ $item->url }}">{{ $item->name}}</a></strong></div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @else
            <p>No watched items found. <a href="/buy">Find something to watch</a>!</p>
        @endif
        <div class="row">
            <div class="medium-12 columns pagination-centered">
                {!! $table->paginator() !!}
            </div>
        </div>
    </div>
</div>
@stop
