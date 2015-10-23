<header>
    <nav class="top-bar" data-topbar role="navigation">
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
                    <a href="/"><i class="fa fa-arrow-circle-left"></i> Leave admin area</a>
                </li>
                <li>
                    @if (!app()->environment('production'))
                    <a href="/">Development environment: <span class="label alert radius">{{ app()->environment() }}</span></a>
                    @endif
                </li>
            </ul>
            <ul class="right">
                <li class="has-dropdown not-click">
                    @include('mustard::fragments.account-menu')
                </li>
            </ul>
        </section>
    </nav>
    @if ($errors->any())
        @include('mustard::fragments.errors')
    @endif
    @if (isset($message) || $message = session('message'))
        @include('mustard::fragments.message')
    @endif
</header>
