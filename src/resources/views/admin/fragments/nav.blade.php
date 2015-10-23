<div class="icon-bar vertical six-up">
    <span class="item title">Admin area</span>
    <a class="item {{ Request::is('admin/dashboard') ? 'active' : '' }}" href="/admin/dashboard">
        <i class="fa fa-tachometer"></i>
        <label>Dashboard</label>
    </a>
    <a class="item {{ Request::is('admin/items') ? 'active' : '' }}" href="/admin/items">
        <i class="fa fa-tags"></i>
        <label>Items</label>
    </a>
    <a class="item {{ Request::is('admin/users') ? 'active' : '' }}" href="/admin/users">
        <i class="fa fa-users"></i>
        <label>Users</label>
    </a>
    <a class="item {{ Request::is('admin/settings') ? 'active' : '' }}" href="/admin/settings">
        <i class="fa fa-cogs"></i>
        <label>Settings</label>
    </a>
    <a class="item {{ Request::is('admin/mailout') ? 'active' : '' }}" href="/admin/mailout">
        <i class="fa fa-paper-plane"></i>
        <label>Mailout</label>
    </a>
</div>
