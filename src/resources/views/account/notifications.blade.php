@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Notifications - Account settings
@stop

@section('content')
<div class="row">
    <div class="medium-2 columns">
        @include('mustard::account.nav')
    </div>
    <div class="medium-10 columns">
        <form method="post" action="/account/notifications" class="content" id="notifications">
            <fieldset>
                <div class="row">
                    <div class="medium-12 columns">
                        <input type="checkbox" disabled checked />
                        <label>Send me security-related notifications</label>
                    </div>
                </div>
                <div class="row">
                    <div class="medium-4 columns">
                        <button class="button expand radius"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
@stop
