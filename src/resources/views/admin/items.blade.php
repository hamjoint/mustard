@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Items - Admin
@stop

@section('content')
    <div class="admin-items">
        <div class="row">
            <div class="medium-3 large-2 columns">
                @include('mustard::admin.fragments.nav')
            </div>
            <div class="medium-9 large-10 columns">
                @include('tablelegs::filter')
                @if (!$table->isEmpty())
                    <table class="expand">
                        @include('tablelegs::header')
                        <tbody>
                            @foreach ($items as $item)
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
                @else
                    <p>Nothing found.</p>
                @endif
                <div class="row">
                    <div class="medium-12 columns pagination-centered">
                        {!! $table->paginator() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
