@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Items - Admin
@stop

@section('content')
    <div class="admin-items">
        <div class="row">
            <div class="medium-2 columns">
                @include('mustard::admin.fragments.nav')
            </div>
            <div class="medium-10 columns">
                @include('tablelegs::filter')
                @if ($table->hasRows())
                    <table class="expand">
                        @include('tablelegs::header')
                        <tbody>
                            @foreach ($table->getRows() as $item)
                            <tr>
                                <td>{{ $item->itemId }}</td>
                                <td><a href="{{ $item->url }}">{{ $item->name }}</a></td>
                                <td>@include('mustard::user.link', ['user' => $item->seller])</a></td>
                                <td>{{ mustard_time($item->getStartingIn(), 2, true) }}</td>
                                <td>{{ mustard_time($item->getTimeLeft(), 2, true) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="medium-12 columns pagination-centered">
                            {!! $table->getPaginator()->render() !!}
                        </div>
                    </div>
                @else

                @endif
            </div>
        </div>
    </div>
@stop
