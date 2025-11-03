<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="/" class="brand-link">
    <span class="brand-text font-weight-light">AdminLTE</span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

        @php
          $roles = $user['roles'] ?? [];
          $perms = $user['permissions'] ?? [];
        @endphp

        @if(in_array('admin', $roles))
          <li class="nav-item">
            <a href="/admin" class="nav-link">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>Admin Panel</p>
            </a>
          </li>
        @endif

        @if(in_array('view dashboard', $perms))
          <li class="nav-item">
            <a href="/dashboard" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
        @endif

      </ul>
    </nav>
  </div>
</aside>
