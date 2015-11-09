{{ mustard_price($delivery_option->price) }}: {{ $delivery_option->name }}
@if (!$delivery_option->maxArrivalTime)
    (>{{ $delivery_option->minArrivalTime }} days)
@elseif (!$delivery_option->minArrivalTime)
    (<{{ $delivery_option->maxArrivalTime }} days)
@elseif ($delivery_option->minArrivalTime == $delivery_option->maxArrivalTime)
    ({{ $delivery_option->minArrivalTime }} days)
@else
    ({{ $delivery_option->minArrivalTime }}-{{ $delivery_option->maxArrivalTime }} days)
@endif
