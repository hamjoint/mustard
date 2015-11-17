@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Mailout - Admin
@stop

@section('content')
    <div class="admin-mailout">
        <div class="row">
            <div class="medium-3 large-2 columns">
                @include('mustard::admin.fragments.nav')
            </div>
            <div class="medium-9 large-10 columns">
                <form method="post" action="/admin/mailout" data-abide="true">
                    {!! csrf_field() !!}
                    <fieldset>
                        <legend>Users</legend>
                        <div class="row">
                            <div class="medium-12 columns">
                                <label>Choose users
                                    <select name="users[]" multiple required>
                                        @foreach ($users as $user)
                                        <option value="{{ $user->userId }}" selected>{{ $user->username }} &lt;{{ $user->email }}&gt;</option>
                                        @endforeach
                                    </select>
                                </label>
                                <small class="error">Please choose users to contact.</small>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Message</legend>
                        <div class="row">
                            <div class="medium-12 columns">
                                <label>Subject line
                                    <input type="text" name="subject" required />
                                </label>
                                <small class="error">Please enter a subject line.</small>
                            </div>
                            <div class="medium-12 columns">
                                <label>Message body
                                    <textarea name="body" required></textarea>
                                </label>
                                <small class="error">Please enter a message.</small>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row">
                        <div class="medium-12 columns">
                            <button class="button expand radius">Send</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
