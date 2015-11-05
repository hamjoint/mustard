@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Users - Admin
@stop

@section('content')
    <div class="admin-users">
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
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->userId }}</td>
                                    <td>@include('mustard::user.link', ['user' => $user])</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ mustard_time($user->getSinceJoined(), 1) }} ago</td>
                                    <td>{{ mustard_time($user->getSinceLastLogin(), 1) }} ago</td>
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
