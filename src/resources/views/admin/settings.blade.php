@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Settings - Admin
@stop

@section('content')
    <div class="admin-settings">
        <div class="row">
            <div class="medium-3 large-2 columns">
                @include('mustard::admin.fragments.nav')
            </div>
            <div class="medium-9 large-10 columns">
                @if (!$table->isEmpty())
                    <table class="expand">
                        @include('tablelegs::header')
                        <tbody>
                            @foreach ($settings as $setting)
                                <tr>
                                    <td>{{ $setting->key }}</td>
                                    <td>
                                        <pre>{{ $setting->value }}</pre>
                                    </td>
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
