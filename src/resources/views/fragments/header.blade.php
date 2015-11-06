<header>
    <nav class="top-bar" data-topbar>
        <ul class="title-area">
            <li class="name">
                <h1><a href="/">Mustard</a></h1>
            </li>
            <li class="toggle-topbar menu-icon">
                <a href="#"><span></span></a>
            </li>
        </ul>
        <section class="top-bar-section">
            <ul class="left">
                <li>
                    <a href="/buy"><i class="fa fa-tag"></i> Buy</a>
                </li>
                <li>
                    <a href="/sell"><i class="fa fa-gavel"></i> Sell</a>
                </li>
                <li class="has-form">
                    <form method="GET" action="{{ Request::is('buy/*') ? "" : "/buy" }}">
                        <div class="row collapse">
                            <div class="large-8 small-9 columns">
                                <input type="search" name="q" value="{{ Input::get('q') }}" placeholder="Enter a search term" />
                            </div>
                            <div class="large-4 small-3 columns">
                                <button type="submit" class="alert button expand">Search</button>
                            </div>
                        </div>
                    </form>
                </li>
                <li>
                    @if (!app()->environment('production'))
                    <a href="/">Development environment: <span class="label alert radius">{{ app()->environment() }}</span></a>
                    @endif
                </li>
            </ul>
            <ul class="right">
                @if (Auth::guest())
                    <li><a href="/auth/register"><i class="fa fa-check-square-o"></i> Register an account</a></li>
                    <li><a href="/auth/login"><i class="fa fa-sign-in"></i> Log in</a></li>
                @else
                    <li>
                        <a href="/admin"><i class="fa fa-briefcase"></i> Admin</a>
                    </li>
                    <li class="has-dropdown not-click">
                        @include('mustard::fragments.account-menu')
                    </li>
                @endif
            </ul>
        </section>
    </nav>
    @if ($errors->any())
        @include('mustard::fragments.errors')
    @endif
    @if (isset($sessage) || $sessage = session('message'))
        @include('mustard::fragments.sessage')
    @endif
</header>
