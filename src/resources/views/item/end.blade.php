@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Ending options: {{ $item->name }}
@stop

@section('content')
    <div class="item-end">
        <div class="row">
            <div class="medium-12 columns">
                <h1>Ending options: {{ $item->name }}</h1>
            </div>
        </div>
        <div class="row">
            <div class="medium-6 medium-offset-3 columns">
                <form method="post" action="/item/cancel" data-abide="true">
                    <input type="hidden" name="item_id" value="{{ $item->itemId }}" />
                    <fieldset>
                        <legend>Cancel item</legend>
                        @if ($item->isAuction())
                        <p>Cancelling this item will withdraw it from sale - any existing bids will be cancelled.</p>
                        @else
                        <p>Cancelling this item will withdraw it from sale - it will no longer be purchaseable.</p>
                        @endif
                        <button class="button alert expand radius"><i class="fa fa-times"></i> Cancel item</button>
                    </fieldset>
                </form>
            </div>
        </div>
        @if ($item->isAuction() && $item->bids()->count())
        <div class="row">
            <div class="medium-6 medium-offset-3 columns">
                <form method="post" action="/item/end" data-abide="true">
                    <input type="hidden" name="item_id" value="{{ $item->itemId }}" />
                    <fieldset>
                        <legend>End item early</legend>
                        <p>The auction will be concluded early - the highest bidder will immediately win the auction for its current price.</p>
                        <button class="button expand radius"><i class="fa fa-gavel"></i> End item early</button>
                    </fieldset>
                </form>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="medium-12 columns">
                <a href="{{ $item->getUrl() }}"><i class="fa fa-arrow-circle-left"></i> Return to item</a>
            </div>
        </div>
    </div>
@stop
