@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Welcome
@stop

@section('content')
<div class="front">
    <div class="row">
        <div class="medium-12 columns">
            <h1>Your Mustard installation is working!</h1>
            <p>You can log in as "{{ Hamjoint\Mustard\User::first()->email }}" using the password "password".</p>
        </div>
    </div>
</div>
@endsection

