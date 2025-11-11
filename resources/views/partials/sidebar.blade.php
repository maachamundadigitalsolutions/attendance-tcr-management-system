<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="/" class="brand-link">
    <span class="brand-text font-weight-light">AdminLTE</span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

        {{-- Admin role items --}}
        <li class="nav-item" data-role="admin" style="display:none;">
          <a href="/dashboard" class="nav-link" wire:navigate>
            <i class="nav-icon fas fa-user-shield"></i>
            <p>Admin Dashboard</p>
          </a>
        </li>

        <li class="nav-item" data-role="admin" style="display:none;">
          <a href="/users" class="nav-link" wire:navigate>
            <i class="nav-icon fas fa-users"></i>
            <p>Users</p>
          </a>
        </li>

        <li class="nav-item" data-role="admin" style="display:none;">
          <a href="/roles" class="nav-link" wire:navigate>
            <i class="nav-icon fas fa-key"></i>
            <p>Roles & Permissions</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
