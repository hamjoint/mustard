<a><i class="fa fa-bars"></i>  {{ Auth::user()->username }}</a>
<ul class="dropdown">
    <li><a href="/inventory"><i class="fa fa-book"></i> Inventory</a></li>
    <li><a href="{{ Auth::user()->url }}"><i class="fa fa-user"></i> View profile</a></li>
    @if (mustard_loaded('messaging'))
        <li><a href="/messages"><i class="fa fa-inbox"></i> Messages</a></li>
    @endif
    <li><a href="/account"><i class="fa fa-sliders"></i> Account settings</a></li>
    <li><a href="/account/close"><i class="fa fa-eject"></i> Close account</a></li>
    <li><a href="/auth/logout"><i class="fa fa-sign-out"></i> Log out</a></li>
</ul>
