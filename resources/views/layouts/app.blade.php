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
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>


{{-- Custom axios setup --}}
<script src="/js/axios.min.js"></script>
<script>
  window.API_URL = "{{ config('app.api_url') }}";
</script>
<script src="{{ asset('js/axios-setup.js') }}"></script>

<script>
  window.userRoles = [];
  window.userPerms = [];

  // 🔧 Global reusable function
  function updateSidebarLogic(res) {
    window.userRoles = res.data.roles || [];
    window.userPerms = (res.data.permissions || []).map(p => p.name);

    const attendance = res.data.attendance_today || {};
    const punchedIn = attendance.punched_in;
    const punchedOut = attendance.punched_out;

    const currentPath = window.location.pathname;
    const attendancePath = "{{ route('attendances') }}";

    // ✅ Redirect only if NOT admin
    if (!userRoles.includes('admin')) {
      if (!punchedIn) {
        if (!currentPath.includes('/attendances')) {
          window.location.href = attendancePath;
          return;
        }
      } else if (punchedOut) {
        if (!currentPath.includes('/attendances')) {
          window.location.href = attendancePath;
          return;
        }
      }
    }

    // ✅ Sidebar nav items toggle
    document.querySelectorAll('.nav-item').forEach(el => {
      const roleAttr = el.getAttribute('data-role');
      const permAttr = el.getAttribute('data-permission');
      const isAttendanceModule = el.classList.contains('attendance-nav');

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

      if (isAttendanceModule) {
        el.style.display = 'block'; // Attendance always visible
      } else {
        if (userRoles.includes('admin')) {
          el.style.display = 'block'; // Admin always sees modules
        } else {
          if (punchedIn && !punchedOut) {
            if (roleOk && permOk) el.style.display = 'block';
          } else {
            el.style.display = 'none';
          }
        }
      }
    });
  }

  // 🔄 Run on page load
  document.addEventListener("DOMContentLoaded", () => {
    const token = localStorage.getItem('api_token');
    if (!token) {
      window.location.href = "{{ route('login') }}";
      return;
    }

    api.get('/me')
      .then(updateSidebarLogic)
      .catch(err => console.error("Error loading user data:", err));
  });

  // 🔄 Run again whenever SPA navigation happens
  document.addEventListener("livewire:navigated", () => {
    api.get('/me')
      .then(updateSidebarLogic)
      .catch(err => console.error("Error loading user data:", err));
  });

  // 🔄 Helper to refresh sidebar after Punch In/Out
  async function refreshSidebar() {
    try {
      const meRes = await api.get('/me');
      updateSidebarLogic(meRes);
    } catch (err) {
      console.error("Sidebar refresh error:", err);
    }
  }

  // Example usage inside Punch In / Punch Out
  window.punchIn = async function() {
    try {
      const formData = new FormData();
      const photo = document.getElementById('in_photo').files[0];
      if (photo) formData.append('in_photo', photo);

      const res = await api.post('/attendances/punch-in', formData);
      $('#attendanceModal').modal('hide');
      await loadAttendances();

      // ✅ Refresh sidebar immediately
      await refreshSidebar();

      Swal.fire({ toast:true, position:'top-end', icon:'success', title:res.data.message, showConfirmButton:false, timer:3000 });
    } catch (err) {
      Swal.fire({ toast:true, position:'top-end', icon:'error', title: err.response?.data?.message || 'Error punching in', showConfirmButton:false, timer:3000 });
    }
  }

  window.punchOut = async function(id) {
    try {
      const formData = new FormData();
      const photo = document.getElementById('out_photo').files[0];
      if (photo) formData.append('out_photo', photo);

      const res = await api.post(`/attendances/${id}/punch-out`, formData);
      $('#attendanceModal').modal('hide');
      await loadAttendances();

      // ✅ Refresh sidebar immediately
      await refreshSidebar();

      Swal.fire({ toast:true, position:'top-end', icon:'success', title:res.data.message, showConfirmButton:false, timer:3000 });
    } catch (err) {
      Swal.fire({ toast:true, position:'top-end', icon:'error', title: err.response?.data?.message || 'Error punching out', showConfirmButton:false, timer:3000 });
    }
  }
</script>

@livewireScripts

{{-- Inject child scripts --}}
@stack('scripts')

</body>
</html>
