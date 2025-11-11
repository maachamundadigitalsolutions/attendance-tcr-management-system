<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel App') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- AdminLTE + Bootstrap CSS --}}
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    {{-- Livewire Styles --}}
    @livewireStyles
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    {{-- Header --}}
  @persist('header')
    @include('partials.header')
  @endpersist

    {{-- Sidebar --}}
  @persist('sidebar')
  <div id="sidebar-container" style="display:none;">
    @include('partials.sidebar')
  </div>
  @endpersist

    {{-- Content Wrapper (this part swaps on navigation) --}}
    <div class="content-wrapper">
        <section class="content pt-3">
            <div class="container-fluid">
                {{ $slot }}
            </div>
        </section>
    </div>

    {{-- Footer --}}
  @persist('footer')
  @include('partials.footer')
  @endpersist

</div>

{{-- jQuery & Bootstrap --}}
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

{{-- Axios --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
  axios.defaults.baseURL = 'http://127.0.0.1:8000/api/v1';
  axios.defaults.headers.common['Accept'] = 'application/json';

  const token = localStorage.getItem('api_token');

  if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    axios.get('/me')
      .then(res => {
        // Show sidebar
        document.getElementById('sidebar-container').style.display = 'block';

        // Toggle items by role
        res.data.roles.forEach(role => {
          document.querySelectorAll(`[data-role="${role}"]`)
            .forEach(el => el.style.display = 'block');
        });

        // Toggle items by permission
        res.data.permissions.forEach(perm => {
          document.querySelectorAll(`[data-permission="${perm}"]`)
            .forEach(el => el.style.display = 'block');
        });
      })
      .catch(() => {
        localStorage.clear();
        window.location.href = "{{ route('login') }}";
      });
  } else {
    window.location.href = "{{ route('login') }}";
  }
</script>

{{-- Livewire Scripts --}}
@livewireScripts
</body>
</html>
