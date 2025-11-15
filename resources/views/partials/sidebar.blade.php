<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="/" class="brand-link">
    <span class="brand-text font-weight-light">TeamTracker</span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" role="menu">

        <li class="nav-item">
          <a href="/dashboard" class="nav-link" wire:navigate>
            <i class="nav-icon fas fa-user-shield"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-item" data-role="admin" style="display:none;">
          <a href="/user-management" class="nav-link" wire:navigate>
            <i class="nav-icon fas fa-users"></i>
            <p>User Management</p>
          </a>
        </li>

        <li class="nav-item" data-role="admin" style="display:none;">
          <a href="/roles-permissions" class="nav-link" wire:navigate>
            <i class="nav-icon fas fa-key"></i>
            <p>Roles & Permissions</p>
          </a>
        </li>
       <li class="nav-item" 
          data-role="admin" 
          data-permission="attendance-mark" 
          style="display:none;">
        <a href="/attendances-management" class="nav-link" wire:navigate>
          <i class="nav-icon fas fa-id-badge"></i>
          <p>Attendances</p>
        </a>
      </li>
            <li class="nav-item" 
          data-role="admin" 
          data-permission="view attendances" 
          style="display:none;">
        <a href="/attendances-management" class="nav-link" wire:navigate>
          <i class="nav-icon fas fa-id-badge"></i>
          <p>TCR Management</p>
        </a>
      </li>

      </ul>
    </nav>
  </div>
</aside>
