<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel App') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- AdminLTE + Bootstrap 5 CSS --}}
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    {{-- Livewire Styles --}}
    @livewireStyles
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    {{-- Header --}}
    @include('partials.header')

    {{-- Sidebar only if user available --}}
    @if(isset($user))
        @include('partials.sidebar')
    @endif

    {{-- Content Wrapper --}}
    <div class="content-wrapper">
        <section class="content pt-3">
            <div class="container-fluid">
                {{ $slot }}   {{-- Livewire component content inject thase --}}
            </div>
        </section>
    </div>

    {{-- Footer --}}
    @include('partials.footer')

</div>

{{-- jQuery & Bootstrap 5 --}}
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

{{-- AdminLTE App --}}
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

{{-- Livewire Scripts --}}
@livewireScripts
</body>
</html>
