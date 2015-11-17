@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Listing Durations - Admin
@stop

@section('content')
    <div class="admin-listing-durations">
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
                            @foreach ($listing_durations as $listing_duration)
                                <tr>
                                    <td>{{ $listing_duration->listingDurationId }}</td>
                                    <td>{{ mustard_time($listing_duration->getDuration()) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No listing durations.</p>
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
