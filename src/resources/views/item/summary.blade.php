@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    {{ $item->name }}
@stop

@section('item-description')
    <h2>Description</h2>
    {!! mustard_markdown($item->description) !!}
@stop

@section('content')
    <div class="item">
        <div class="row">
            <div class="medium-12 columns">
                <h1>{{ $item->name }}</h1>
            </div>
            @if (mustard_loaded('auctions') && $item->auction && $item->isEnded())
                @if ($item->isReserved())
                    <div class="medium-12 columns">
                        <div class="alert-box secondary radius">
                            This auction has ended - the reserve price was not met.
                        </div>
                    </div>
                @elseif ($item->hasBids() && !$item->winningBid)
                    <div class="medium-12 columns">
                        <div class="alert-box secondary radius">
                            This auction has ended and will be processed shortly.
                        </div>
                    </div>
                @elseif (Auth::check() && $item->isWinner(Auth::user()))
                    <div class="medium-12 columns">
                        <div class="alert-box success radius">
                            Congratulations! You won this item. <a href="/inventory/bought">View your purchases</a>.
                        </div>
                    </div>
                @elseif (Auth::check() && $item->isBidder(Auth::user()))
                    <div class="medium-12 columns">
                        <div class="alert-box secondary radius">
                            This auction has ended - you were outbid.
                        </div>
                    </div>
                @else
                    <div class="medium-12 columns">
                        <div class="alert-box secondary radius">
                            This auction has ended.
                        </div>
                    </div>
                @endif
            @endif
            @if (mustard_loaded('commerce') && !$item->auction && Auth::check() && $item->isBuyer(Auth::user()))
                <div class="medium-12 columns">
                    <div class="alert-box success radius">
                        You bought this item. <a href="/inventory/bought">View your purchases</a>.
                    </div>
                </div>
            @endif
            @if (!$item->auction && $item->isEnded())
                <div class="medium-12 columns">
                    <div class="alert-box secondary radius">
                        This item is no longer available.
                    </div>
                </div>
            @endif
        </div>
        <div class="row">
            @if (mustard_loaded('media'))
                <div class="medium-6 columns gallery">
                    <div class="gallery-display">
                        @foreach ($photos as $photo)
                            <div><img src="{{ $photo->urlLarge }}" /></div>
                        @endforeach
                    </div>
                    <div class="gallery-nav">
                        @foreach ($photos as $photo)
                            @if ($photo->exists)
                                <div><img src="{{ $photo->urlSmall }}" /></div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <div class="medium-6 columns">
                    @yield('item-description')
                </div>
            @endif
            <div class="medium-3 columns">
                <ul class="pricing-table">
                    @if (mustard_loaded('auctions') && $item->auction)
                        <li class="price">{{ mustard_price($item->biddingPrice, true) }}</li>
                        <li class="description"><a href="/item/bid/{{ $item->itemId }}">{{ mustard_number($bids->count(), 0) }} bids</a></li>
                        @if ($item->isActive())
                            @if (Auth::check() && $highest_bid->bidder == Auth::user())
                                @if ($item->isReserved())
                                    <li class="cta-button">
                                        <div class="alert-box warning radius">
                                            You are currently the highest bidder, but the reserve price has not yet been reached.
                                        </div>
                                    </li>
                                @else
                                    <li class="cta-button">
                                        <div class="alert-box success radius">
                                            You are currently the highest bidder.
                                        </div>
                                    </li>
                                @endif
                            @elseif (Auth::check() && $item->isBidder(Auth::user()))
                                <li class="cta-button">
                                    <div class="alert-box alert radius">
                                        You have been outbid.
                                    </div>
                                </li>
                            @endif
                            <li class="cta-button"><a class="button expand radius" href="/item/bid/{{ $item->itemId }}">Bid Now</a></li>
                            @if ($item->hasFixed())
                                <li class="cta-button"><a class="button expand radius" href="/checkout/{{ $item->itemId }}">Buy Now: {{ mustard_price($item->fixedPrice, true) }}</a></li>
                            @endif
                        @endif
                    @elseif (!$item->auction)
                        <li class="price">{{ mustard_price($item->fixedPrice, true) }}</li>
                        @if ($item->isActive())
                            <li class="cta-button"><a class="button expand radius" href="/checkout/{{ $item->itemId }}">Buy Now</a></li>
                        @endif
                    @endif
                    @if ($item->isActive())
                        <li class="cta-button">
                            <form method="post" action="/item/watch">
                                {!! csrf_field() !!}
                                <input type="hidden" name="item_id" value="{{ $item->itemId }}" />
                                @if (Auth::check() && Auth::user()->isWatching($item))
                                    <button class="button expand radius" disabled><i class="fa fa-binoculars"></i> Watching</button>
                                @else
                                    <button class="button expand radius"><i class="fa fa-binoculars"></i> Watch</button>
                                @endif
                            </form>
                        </li>
                    @elseif (mustard_loaded('auctions') && Auth::check() && $highest_bid->bidder == Auth::user())
                        <li class="cta-button"><a class="button success expand radius" href="/checkout/{{ $item->itemId }}">Pay</a></li>
                    @endif
                    <li class="bullet-item"><strong>Condition:</strong>
                        <span>{{ $item->condition->name }}</span>
                    </li>
                    @if ($item->hasQuantity())
                        <li class="bullet-item"><strong>Quantity available:</strong>
                            <span>{{ $item->quantity }}</span>
                        </li>
                    @endif
                    @if ($item->isActive())
                        <li class="bullet-item"><strong>Time left:</strong>
                            <span>{{ mustard_time($item->getTimeLeft()) }}</span>
                        </li>
                    @endif
                    <li class="bullet-item"><strong>End date:</strong>
                        <span>{{ mustard_datetime($item->endDate) }}</span>
                    </li>
                </ul>
            </div>
            <div class="medium-3 columns">
                <h2>Seller</h2>
                @include('mustard::user.link', ['user' => $item->seller])
                <h2>Payment options</h2>
                <ul class="no-bullet">
                    <li>
                        <i class="fa fa-3x fa-cc-visa"></i>
                        <i class="fa fa-3x fa-cc-mastercard"></i>
                        <i class="fa fa-3x fa-cc-amex"></i>
                    </li>
                    @if ($item->paymentOther)
                        <li>Cash on collection</li>
                    @endif
                </ul>
            </div>
        </div>
        @if (mustard_loaded('media'))
            <div class="row">
                <div class="medium-12 columns">
                    @yield('item-description')
                </div>
            </div>
        @endif
        <div class="row">
            <div class="medium-12 columns">
                <h2>Delivery options</h2>
                <table class="expand">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Price</th>
                            <th>Delivery estimate (from date of payment)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item->deliveryOptions as $delivery_option)
                            <tr>
                                <td>{{ $delivery_option->name }}</td>
                                <td>{{ mustard_price($delivery_option->price, true) }}</td>
                                <td>{{ $delivery_option->humanArrivalTime }}</td>
                            </tr>
                        @endforeach
                        @if ($item->isCollectable())
                            <tr>
                                <td>Collection from {{ $item->collectionLocation }}</td>
                                <td>Free</td>
                                <td>-</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="medium-12 columns">
                <h2>Returns</h2>
                @if ($item->returnsPeriod)
                    Returns are accepted up to {{ mustard_number($item->returnsPeriod, 0) }} days from the date of delivery, as long as the item is in its original condition.
                @else
                    Returns are not accepted.
                @endif
            </div>
        </div>
    </div>
@stop
