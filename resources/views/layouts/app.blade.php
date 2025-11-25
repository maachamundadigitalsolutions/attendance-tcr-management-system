<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ config('app.name', 'Laravel App') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- AdminLTE + Bootstrap CSS --}}
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

  {{-- DataTables CSS --}}
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

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
    @include('partials.sidebar')
  @endpersist

  {{-- Content Wrapper --}}
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

{{-- DataTables JS --}}
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

{{-- Custom axios setup --}}
<script src="/js/axios.min.js"></script>
<script src="{{ asset('js/axios-setup.js') }}"></script>

<script>
  window.userRoles = [];
  window.userPerms = [];

document.addEventListener("DOMContentLoaded", () => {

  const token = localStorage.getItem('api_token');
  if (!token) {
    window.location.href = "{{ route('login') }}";
    return;
  }

  axios.get('/me')
    .then(res => {
      window.userRoles = res.data.roles || [];
      window.userPerms = res.data.permissions || [];
      

      document.querySelectorAll('.nav-item').forEach(el => {
        const roleAttr = el.getAttribute('data-role');
        const permAttr = el.getAttribute('data-permission');

        let roleOk = true;
        if (roleAttr) {
          const requiredRoles = roleAttr.split(',').map(r => r.trim());
          roleOk = requiredRoles.some(r => userRoles.includes(r));
        }

        let permOk = true;
        if (permAttr) {
          const requiredPerms = permAttr.split(',').map(p => p.trim());
          permOk = requiredPerms.some(p => userPerms.includes(p));
        }

        if (roleOk && permOk) {
          el.style.display = 'block';
        }
      });

      // ✅ Bulk assign card toggle
      if (perms.includes('tcr-assign')) {
        const card = document.getElementById('bulkAssignCard');
        if (card) {
          card.style.display = 'block';
        }
      }
    })
    .catch(err => {
      console.error("Bulk assign error:", err.response ? err.response.data : err);
      alert("Bulk assign failed");
    });
});



</script>

@livewireScripts

{{-- Inject child scripts --}}
@stack('scripts')

</body>
</html>
