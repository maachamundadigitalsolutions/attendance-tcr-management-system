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
      </ul>
    </nav>
  </div>
</aside>
