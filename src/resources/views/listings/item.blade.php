<div class="row listings-item">
    @if (mustard_loaded('media'))
        <div class="small-2 columns">
            <a href="{{ $item->url }}"><img src="{{ $item->getListingPhoto()->urlSmall }}" /></a>
        </div>
    @endif
    <div class="small-{{ mustard_loaded('media') ? 7 : 9 }} columns">
        <strong><a href="{{ $item->url }}">{{ $item->name }}</a></strong>
        <br />
        @if (mustard_loaded('auctions') && $item->auction)
            {{ mustard_price($item->biddingPrice) }} ({{ mustard_number($item->bids->count()) }} bids)
            @if ($item->hasFixed())
                <br />or buy now for {{ mustard_price($item->fixedPrice ?: '-') }}
            @endif
        @else
            {{ mustard_price($item->fixedPrice ?: '-') }}
        @endif
    </div>
    <div class="small-3 columns">{{ mustard_time($item->getTimeLeft(), 2, true) }} left</div>
</div>
