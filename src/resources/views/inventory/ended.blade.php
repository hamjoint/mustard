@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Ended - Inventory
@stop

@section('content')
<div class="row">
    <div class="medium-2 columns">
        @include('mustard::inventory.nav')
    </div>
    <div class="medium-10 columns">
        @if (!$table->isEmpty())
            <table class="expand">
                @include('tablelegs::header')
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item->itemId }}</td>
                            <td><a href="{{ $item->url }}">{{ $item->name }}</a></td>
                            <td>{{ mustard_time($item->getDuration(), 2, true) }}</td>
                            <td style="white-space:nowrap;">
                                <strong>Fixed price:</strong> {{ mustard_price($item->fixedPrice) }}<br />
                                <strong>Quantity:</strong> {{ $item->quantity }}
                            </td>
                            <td>{{ mustard_time($item->getTimeLeft(), 2, true) }}</td>
                            <td>
                                <button href="#" data-dropdown="item-{{ $item->itemId }}-options" aria-controls="item-{{ $item->itemId }}-options" aria-expanded="false" class="button tiny radius dropdown"><i class="fa fa-cog"></i></button>
                                <ul id="item-{{ $item->itemId }}-options" data-dropdown-content class="f-dropdown" aria-hidden="true" tabindex="-1">
                                    <li><a href="/item/relist/{{ $item->itemId }}">Relist</a></li>
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row">
                <div class="medium-12 columns pagination-centered">
                    {!! $table->paginator() !!}
                </div>
            </div>
        @else
            <p>You haven't got any unsold items. <a href="/sell">Sell one now</a>.</p>
        @endif
    </div>
</div>
@stop
