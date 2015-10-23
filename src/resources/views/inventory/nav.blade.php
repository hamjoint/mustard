<div class="icon-bar vertical six-up">
    <span class="item title">Inventory</span>
    <a class="item {{ Request::is('inventory/watching') ? 'active' : '' }}" href="/inventory/watching">
        <i class="fa fa-binoculars"></i>
        <label>Watching</label>
    </a>
    <a class="item {{ Request::is('inventory/bidding') ? 'active' : '' }}" href="/inventory/bidding">
        <i class="fa fa-thumbs-up"></i>
        <label>Bidding</label>
    </a>
    <a class="item {{ Request::is('inventory/bought') ? 'active' : '' }}" href="/inventory/bought">
        <i class="fa fa-star"></i>
        <label>Bought &amp; Won</label>
    </a>
    <a class="item {{ Request::is('inventory/selling') ? 'active' : '' }}" href="/inventory/selling">
        <i class="fa fa-tags"></i>
        <label>Selling</label>
    </a>
    <a class="item {{ Request::is('inventory/scheduled') ? 'active' : '' }}" href="/inventory/scheduled">
        <i class="fa fa-clock-o"></i>
        <label>Scheduled</label>
    </a>
    <a class="item {{ Request::is('inventory/sold') ? 'active' : '' }}" href="/inventory/sold">
        <i class="fa fa-gavel"></i>
        <label>Sold</label>
    </a>
    <a class="item {{ Request::is('inventory/unsold') ? 'active' : '' }}" href="/inventory/unsold">
        <i class="fa fa-umbrella"></i>
        <label>Unsold</label>
    </a>
</div>
