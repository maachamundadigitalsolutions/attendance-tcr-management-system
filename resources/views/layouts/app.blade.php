<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ config('app.name', 'Laravel') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- AdminLTE 3.2 CSS --}}
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

  @livewireStyles
  <style>
    /* Optional: smoother theme switch */
    body { transition: background-color .2s, color .2s; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  {{-- Navbar --}}
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('dashboard') }}" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <button id="themeToggle" class="btn btn-sm btn-outline-secondary">
          <i class="fas fa-adjust"></i>
        </button>
      </li>
      <li class="nav-item ml-2">
        <a class="nav-link" href="#">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </nav>

  {{-- Sidebar --}}
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand -->
    <a href="{{ route('dashboard') }}" class="brand-link">
      <span class="brand-text font-weight-light">{{ config('app.name', 'Admin') }}</span>
    </a>

    <!-- Sidebar menu -->
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>Users</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tasks"></i>
              <p>Tasks</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>Reports</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  {{-- Content wrapper --}}
  <div class="content-wrapper">
    <!-- Page header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
          <h1>@yield('title', 'Dashboard')</h1>
          {{-- Optional breadcrumbs --}}
          <ol class="breadcrumb float-sm-right mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        {{ $slot }}
      </div>
    </section>
  </div>

  {{-- Footer --}}
  <footer class="main-footer text-center">
    <strong>&copy; {{ date('Y') }} {{ config('app.name', 'Admin') }}.</strong> All rights reserved.
  </footer>
</div>

{{-- Scripts --}}
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

@livewireScripts

<script>
  // AdminLTE 3.2 dark/light toggle with persistence
  (function() {
    const key = 'adminlte-theme';
    const body = document.body;

    // Apply saved theme
    const saved = localStorage.getItem(key);
    if (saved === 'dark') {
      body.classList.add('dark-mode');
    }

    // Toggle on click
    document.getElementById('themeToggle')?.addEventListener('click', function() {
      body.classList.toggle('dark-mode');
      localStorage.setItem(key, body.classList.contains('dark-mode') ? 'dark' : 'light');
    });
  })();
</script>
</body>
</html>
