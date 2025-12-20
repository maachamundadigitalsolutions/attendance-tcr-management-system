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

  {{-- Content --}}
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

{{-- ================= GLOBAL SCRIPTS ================= --}}

{{-- jQuery --}}
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>

{{-- Bootstrap --}}
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

{{-- AdminLTE --}}
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

{{-- DataTables --}}
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

{{-- DayJS --}}
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>

{{-- SweetAlert (FIXED) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Axios --}}
<script src="{{ asset('js/axios.min.js') }}"></script>
<script>
  window.API_URL = "{{ config('app.api_url') }}";
</script>
<script src="{{ asset('js/axios-setup.js') }}"></script>

{{-- ================= GLOBAL LOGIC ================= --}}

<script>
  window.userRoles = [];
  window.userPerms = [];

  function updateSidebarLogic(res) {
    window.userRoles = res.data.roles || [];
    window.userPerms = (res.data.permissions || []).map(p => p.name);

    const attendance = res.data.attendance_today || {};
    const punchedIn = attendance.punched_in;
    const punchedOut = attendance.punched_out;

    const currentPath = window.location.pathname;
    const attendancePath = "{{ route('attendances') }}";

    if (!userRoles.includes('admin')) {
      if (!punchedIn || punchedOut) {
        if (!currentPath.includes('/attendances')) {
          window.location.href = attendancePath;
          return;
        }
      }
    }

    document.querySelectorAll('.nav-item').forEach(el => {
      const roleAttr = el.getAttribute('data-role');
      const permAttr = el.getAttribute('data-permission');
      const isAttendance = el.classList.contains('attendance-nav');

      let roleOk = true;
      if (roleAttr) {
        roleOk = roleAttr.split(',').some(r => userRoles.includes(r.trim()));
      }

      let permOk = true;
      if (permAttr) {
        permOk = permAttr.split(',').some(p => userPerms.includes(p.trim()));
      }

      if (isAttendance || userRoles.includes('admin')) {
        el.style.display = 'block';
      } else {
        el.style.display = (punchedIn && !punchedOut && roleOk && permOk)
          ? 'block'
          : 'none';
      }
    });
  }

  async function refreshSidebar() {
    try {
      const res = await api.get('/me');
      updateSidebarLogic(res);
    } catch (e) {
      console.error('Sidebar refresh error', e);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('api_token');
    if (!token) {
      window.location.href = "{{ route('login') }}";
      return;
    }

    api.get('/me')
      .then(updateSidebarLogic)
      .catch(err => console.error(err));
  });

  document.addEventListener('livewire:navigated', () => {
    api.get('/me')
      .then(updateSidebarLogic)
      .catch(err => console.error(err));
  });
</script>

{{-- ================= LIVEWIRE & CHILD SCRIPTS ================= --}}

@livewireScripts
@stack('scripts')

</body>
</html>
