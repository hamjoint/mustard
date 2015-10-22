@if ($paginator->getLastPage() > 1)
    <ul class="pagination">
        {{ (new ZurbPresenter($paginator))->render() }}
    </ul>
@endif
