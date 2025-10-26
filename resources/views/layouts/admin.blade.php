<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Dashboard')</title>
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
  @livewireStyles
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  {{-- Navbar --}}
  @include('partials.navbar')

  {{-- Sidebar --}}
  @include('partials.sidebar')

  {{-- Content --}}
  <div class="content-wrapper">
    <section class="content pt-3">
      <div class="container-fluid">
        @yield('content')
      </div>
    </section>
  </div>

  {{-- Footer --}}
  @include('partials.footer')

</div>
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
@livewireScripts
</body>
</html>
