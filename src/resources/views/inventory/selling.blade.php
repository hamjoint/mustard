@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Selling - Inventory
@stop

@section('content')
<div class="row">
    <div class="medium-2 columns">
        @include('mustard::inventory.nav')
    </div>
    <div class="medium-10 columns">
        @include('tablelegs::filter')
        @if (!$table->isEmpty())
            <table class="expand">
                @include('tablelegs::header')
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item->itemId }}</td>
                            <td><a href="{{ $item->url }}">{{ $item->name }}</a></td>
                            <td>{{ mustard_time($item->getDuration(), 2, true) }}</td>
                            @if (mustard_loaded('auctions') && $item->auction)
                                <td style="white-space:nowrap;">
                                    <strong>Bids:</strong> {{ $item->bids->count() }}<br />
                                    <strong>Current price:</strong> {{ mustard_price($item->biddingPrice) }}
                                    @if ($item->hasReserve())
                                        <strong>Reserve price:</strong> {{ mustard_price($item->reservePrice) }}
                                    @endif
                                </td>
                            @else
                                <td style="white-space:nowrap;">
                                    <strong>Fixed price:</strong> {{ mustard_price($item->fixedPrice) }}<br />
                                    <strong>Quantity:</strong> {{ $item->quantity }}
                                </td>
                            @endif
                            <td>{{ mustard_time($item->getTimeLeft(), 2, true) }}</td>
                            <td>
                                <button href="#" data-dropdown="item-{{ $item->itemId }}-options" aria-controls="item-{{ $item->itemId }}-options" aria-expanded="false" class="button tiny radius dropdown"><i class="fa fa-cog"></i></button>
                                <ul id="item-{{ $item->itemId }}-options" data-dropdown-content class="f-dropdown" aria-hidden="true" tabindex="-1">
                                    <li><a href="/item/edit/{{ $item->itemId }}">Edit listing</a></li>
                                    <li><a href="/item/end/{{ $item->itemId }}">End early</a></li>
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>You aren't selling any items. <a href="/sell">Sell one now</a>.</p>
        @endif
        <div class="row">
            <div class="medium-12 columns text-center">
                {{ $table->paginator() }}
            </div>
        </div>
    </div>
</div>
@stop
