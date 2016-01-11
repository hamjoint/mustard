@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Email address - Account settings
@stop

@section('content')
<div class="row">
    <div class="medium-3 large-2 columns">
        @include('mustard::account.nav')
    </div>
    <div class="medium-9 large-10 columns">
        <form method="post" action="/account/email" data-abide="true" class="content" id="email">
            {!! csrf_field() !!}
            <fieldset>
                <div class="row">
                    <div class="medium-4 columns">
                        <label>Your email address
                            <input type="email" name="email" value="{{ Auth::user()->email }}" required pattern="email" />
                        </label>
                        <small class="error">Please enter a valid email address.</small>
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
