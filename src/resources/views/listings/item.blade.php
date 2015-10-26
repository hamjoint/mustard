<div class="row listings-item">
    <div class="small-2 columns">
        <a href="{{ $item->url }}"><img src="{{ $item->getListingPhoto()->smallUrl }}" /></a>
    </div>
    <div class="small-7 columns">
        <strong><a href="{{ $item->url }}">{{ $item->name }}</a></strong>
        <br />
        @if (!$item->auction)
            {{ mustard_price($item->fixedPrice ?: '-') }}
        @else
            {{ mustard_price($item->biddingPrice) }} ({{ mustard_number($item->bids->count()) }} bids)
            @if ($item->hasFixed())
                <br />or buy now for {{ mustard_price($item->fixedPrice ?: '-') }}
            @endif
        @endif
    </div>
    <div class="small-3 columns">{{ mustard_time($item->getTimeLeft(), 2, true) }} left</div>
</div>
