<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="{{ url('/') }}" class="brand-link">
    <span class="brand-text font-weight-light">Team Task Tracker</span>
  </a>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
        
        <li class="nav-item">
          <a href="{{ url('/dashboard') }}" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        @role('admin')
        <li class="nav-item">
          <a href="{{ url('/admin-dashboard') }}" class="nav-link">
            <i class="nav-icon fas fa-user-shield"></i>
            <p>Admin Panel</p>
          </a>
        </li>
        @endrole

        @can('manage users')
        <li class="nav-item">
          <a href="{{ route('users.index') }}" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>Users</p>
          </a>
        </li>
        @endcan

        @can('view reports')
        <li class="nav-item">
          <a href="{{ route('reports.index') }}" class="nav-link">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>Reports</p>
          </a>
        </li>
        @endcan

      </ul>
    </nav>
  </div>
</aside>
