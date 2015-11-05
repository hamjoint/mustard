<div class="icon-bar vertical six-up">
    <span class="item title">Admin area</span>
    <a class="item {{ Request::is('admin/dashboard') ? 'active' : '' }}" href="/admin/dashboard">
        <i class="fa fa-tachometer"></i>
        <label>Dashboard</label>
    </a>
    <a class="item {{ Request::is('admin/users') ? 'active' : '' }}" href="/admin/users">
        <i class="fa fa-users"></i>
        <label>Users</label>
    </a>
    <a class="item {{ Request::is('admin/items') ? 'active' : '' }}" href="/admin/items">
        <i class="fa fa-tags"></i>
        <label>Items</label>
    </a>
    <a class="item {{ Request::is('admin/categories') ? 'active' : '' }}" href="/admin/categories">
        <i class="fa fa-sitemap"></i>
        <label>Categories</label>
    </a>
    <a class="item {{ Request::is('admin/item-conditions') ? 'active' : '' }}" href="/admin/item-conditions">
        <i class="fa fa-eye"></i>
        <label>Item Conditions</label>
    </a>
    <a class="item {{ Request::is('admin/listing-durations') ? 'active' : '' }}" href="/admin/listing-durations">
        <i class="fa fa-calendar"></i>
        <label>Listing Durations</label>
    </a>
    <a class="item {{ Request::is('admin/mailout') ? 'active' : '' }}" href="/admin/mailout">
        <i class="fa fa-paper-plane"></i>
        <label>Mailout</label>
    </a>
    <a class="item {{ Request::is('admin/settings') ? 'active' : '' }}" href="/admin/settings">
        <i class="fa fa-cogs"></i>
        <label>Settings</label>
    </a>
</div>
