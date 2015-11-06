@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Password - Account settings
@stop

@section('content')
<div class="row">
    <div class="medium-2 columns">
        @include('mustard::account.nav')
    </div>
    <div class="medium-10 columns">
        <form method="post" action="/account/password" data-abide="true" class="content active" id="password">
            {!! csrf_field() !!}
            <fieldset>
                <div class="row">
                    <div class="medium-4 columns">
                        <label>Your current password
                            <input type="password" name="old_password" placeholder="For added security" required />
                        </label>
                        <small class="error">Please enter your current password.</small>
                    </div>
                    <div class="medium-4 columns">
                        <label>Your new password
                            <input type="password" id="new-password" name="new_password" placeholder="Choose something long" required />
                        </label>
                        <small class="error">Please enter a new password.</small>
                    </div>
                    <div class="medium-4 columns">
                        <label>Repeat your new password
                            <input type="password" placeholder="Just in case you typo'd" required data-equalto="new-password" />
                        </label>
                        <small class="error">Please repeat the new password.</small>
                    </div>
                </div>
                <div class="row">
                    <div class="medium-4 columns">
                        <button class="button expand radius"><i class="fa fa-check"></i> Change</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
@stop
