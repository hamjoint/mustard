@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Item Conditions - Admin
@stop

@section('content')
    <div class="admin-listing-conditions">
        <div class="row">
            <div class="medium-2 columns">
                @include('mustard::admin.fragments.nav')
            </div>
            <div class="medium-10 columns">
                @include('tablelegs::filter')
                @if (!$table->isEmpty())
                    <table class="expand">
                        @include('tablelegs::header')
                        <tbody>
                            @foreach ($item_conditions as $item_condition)
                                <tr>
                                    <td>{{ $item_condition->itemConditionId }}</td>
                                    <td>{{ $item_condition->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No item conditions.</p>
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
