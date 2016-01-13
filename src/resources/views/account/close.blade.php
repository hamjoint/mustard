@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Close your account
@stop

@section('content')
    <div class="close-account">
        <div class="row">
            <div class="large-12 columns">
                <h1>Close your account</h1>
                <p>If you so choose, you can have your account closed. This is a permanent and irreversible process.</p>
                <h2>What will be deleted?</h2>
                <ul>
                    <li>All your account details except for your username.</li>
                    @if (mustard_loaded('commerce'))
                        <li>Payment details associated with your account.</li>
                    @endif
                    @if (mustard_loaded('feedback'))
                        <li>The association between your account and any feedback you've received.</li>
                    @endif
                    @if (mustard_loaded('messaging'))
                        <li>The association between your account and messages you've sent and received.</li>
                    @endif
                </ul>
                <h2>What will <strong>not</strong> be deleted?</h2>
                <ul>
                    <li>Any listings you've created. Those still running will be ended early without a winner.</li>
                    @if (mustard_loaded('commerce'))
                        <li>Postal addresses associated with your account.</li>
                    @endif
                    @if (mustard_loaded('media'))
                        <li>Any images or videos you've uploaded to listings.</li>
                    @endif
                    @if (mustard_loaded('feedback'))
                        <li>Any feedback you've left for others.</li>
                    @endif
                    @if (mustard_loaded('messaging'))
                        <li>Other users' copies of messages you've sent and received.</li>
                    @endif
                </ul>
            </div>
        </div>
        <form method="post" action="/account/close" data-abide="true">
            {!! csrf_field() !!}
            <div class="row">
                <div class="medium-6 medium-offset-3 large-4 large-offset-4 columns">
                    <label>Please type "close my account" to continue
                        <input type="text" name="confirm" placeholder="close my account" pattern="close_account" />
                    </label>
                    <small class="error">You must complete this text to continue</small>
                </div>
            </div>
            <div class="row">
                <div class="medium-6 medium-offset-3 large-4 large-offset-4 columns">
                    <button class="button expand alert radius"><i class="fa fa-check"></i> Close</button>
                </div>
            </div>
        </form>
    </div>
@stop
