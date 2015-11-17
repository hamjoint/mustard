@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Scheduled - Inventory
@stop

@section('content')
<div class="row">
    <div class="medium-3 large-2 columns">
        @include('mustard::inventory.nav')
    </div>
    <div class="medium-9 large-10 columns">
        @include('tablelegs::filter')
        @if (!$items->isEmpty())
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
                                    <strong>Starting price:</strong> {{ mustard_price($item->startPrice) }}<br />
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
                            <td>{{ mustard_time($item->getStartingIn(), 2) }}</td>
                            <td>
                                <button href="#" data-dropdown="item-{{ $item->itemId }}-options" aria-controls="item-{{ $item->itemId }}-options" aria-expanded="false" class="button tiny radius dropdown"><i class="fa fa-cog"></i></button>
                                <ul id="item-{{ $item->itemId }}-options" data-dropdown-content class="f-dropdown" aria-hidden="true" tabindex="-1">
                                    <li><a href="/item/end/{{ $item->itemId }}">Cancel</a></li>
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>You haven't got any items scheduled. <a href="/sell">Sell one now</a>.</p>
        @endif
        <div class="row">
            <div class="medium-12 columns text-center">
                {{ $table->paginator() }}
            </div>
        </div>
    </div>
</div>
@stop
