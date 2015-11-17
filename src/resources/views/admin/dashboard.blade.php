@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Dashboard - Admin
@stop

@section('content')
    <div class="admin-dashboard">
        <div class="row">
            <div class="medium-3 large-2 columns">
                @include('mustard::admin.fragments.nav')
            </div>
            <div class="medium-9 large-10 columns">
                <div class="row">
                    @foreach ($stats as $stat_cat => $stat_groups)
                        <div class="medium-12 columns end">
                            <h2>{{ $stat_cat }}</h2>
                            <table class="expand">
                                <thead>
                                    <tr>
                                        <th></th>
                                        @foreach ($ranges as $range_name => $range)
                                            <th>{{ $range_name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stat_groups as $stat_group_name => $stat)
                                    <tr>
                                        <th>{{ $stat_group_name }}</th>
                                        @foreach ($ranges as $range)
                                            <td>{{ $stat($range) ?: 0 }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@stop
