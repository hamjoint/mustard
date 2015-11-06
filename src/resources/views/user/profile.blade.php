@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    {{ $user->username }}
@stop

@section('content')
    <div class="profile">
        <div class="row">
            <h1 class="medium-12 columns">{{ $user->username }}</h1>
            <div class="medium-12 columns">
                Joined: {{ mustard_date($user->joined) }}
            </div>
        </div>
        @if (mustard_loaded('feedback'))
            <div class="row">
                <div class="medium-12 columns">
                    <h2>Recent feedback</h2>
                    @if ($feedbacks->count())
                        @foreach ($feedbacks as $feedback)
                            <blockquote>
                                @if ($feedback->isPositive())
                                    <i class="fa fa-plus success"></i>
                                @elseif ($feedback->isNegative())
                                    <i class="fa fa-minus alert"></i>
                                @elseif ($feedback->isNeutral())
                                    <i class="fa fa-circle"></i>
                                @endif
                                {{ $feedback->message }}<cite>@include('mustard::user.link', ['user' => $feedback->rater])</cite>
                            </blockquote>
                        @endforeach
                        <a class="button small pull-right" href="/user/feedback/{{ $user->userId }}">View all feedback</a>
                    @else
                        <p>No feedback received yet.</p>
                    @endif
                </div>
            </div>
        @endif
        <div class="row">
            <div class="medium-12 columns">
                <h2>Items</h2>
                @if ($items->count())
                    @foreach ($items as $item)
                        @include('mustard::listings.item')
                    @endforeach
                @else
                    <p>No items currently for sale.</p>
                @endif
            </div>
        </div>
    </div>
@stop
