<div class="icon-bar vertical six-up">
    <span class="item title">Account settings</span>
    <a class="item {{ Request::is('account/password') ? 'active' : '' }}" href="/account/password">
        <i class="fa fa-key"></i>
        <label>Password</label>
    </a>
    <a class="item {{ Request::is('account/email') ? 'active' : '' }}" href="/account/email">
        <i class="fa fa-at"></i>
        <label>Email address</label>
    </a>
    <a class="item {{ Request::is('account/notifications') ? 'active' : '' }}" href="/account/notifications">
        <i class="fa fa-bell"></i>
        <label>Notifications</label>
    </a>
    @if (mustard_loaded('commerce'))
        <a class="item {{ Request::is('account/postal-addresses') ? 'active' : '' }}" href="/account/postal-addresses">
            <i class="fa fa-envelope"></i>
            <label>Postal addresses</label>
        </a>
        <a class="item {{ Request::is('account/bank-details') ? 'active' : '' }}" href="/account/bank-details">
            <i class="fa fa-bank"></i>
            <label>Bank details</label>
        </a>
    @endif
</div>
