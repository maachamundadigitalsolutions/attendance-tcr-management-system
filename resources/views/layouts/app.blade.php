<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel App') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- AdminLTE CSS --}}
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    {{-- Livewire Styles --}}
    @livewireStyles
</head>
<body class="hold-transition sidebar-mini layout-fixed">

    {{-- Page Content --}}
  <div class="wrapper">

  @include('partials.header')
  @includeWhen(isset($user), 'partials.sidebar')

  <div class="content-wrapper">
  {{ $slot }}
</div>


  @include('partials.footer')

</div>

    {{-- jQuery & Bootstrap --}}
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

    {{-- Livewire Scripts --}}
    @livewireScripts
</body>
</html>
