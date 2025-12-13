<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - {{ config('app.name') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- AdminLTE CSS --}}
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
  @livewireStyles
</head>
<body class="hold-transition login-page">

<div class="login-box" id="loginBox" style="display:none;">
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form id="loginForm">
        <div class="input-group mb-3">
          <input type="text" id="email" class="form-control" placeholder="Email or User ID" />
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-user"></span></div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="password" id="password" class="form-control" placeholder="Password" />
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>

        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" />
              <label for="remember">Remember Me</label>
            </div>
          </div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
        </div>
      </form>
    </div> <!-- /.login-card-body -->
  </div>   <!-- /.card -->
</div>     <!-- /.login-box -->

{{-- Scripts --}}
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
 
{{-- Custom axios setup --}}
<script src="/js/axios.min.js"></script>
<script> window.API_URL = "{{ config('app.api_url') }}"; </script>
<script src="{{ asset('js/axios-setup.js') }}"></script>
<script>

  const token = localStorage.getItem('api_token');
  console.log('token', token);
  

if (token) {
  api.get('/me')
    .then(res => {
      window.userRoles = res.data.roles || [];
      window.userPerms = (res.data.permissions || []).map(p => p.name);

      const attendance = res.data.attendance_today || {};
      const punchedIn = attendance.punched_in;
      const punchedOut = attendance.punched_out;

      const currentPath = window.location.pathname;
      const attendancePath = "{{ route('attendances') }}";

      // ✅ Only apply redirect logic if NOT admin
      if (!userRoles.includes('admin')) {
        if (!punchedIn) {
          // Not punched in → force to Attendance page
          if (!currentPath.includes('/attendances')) {
            window.location.href = attendancePath;
            return;
          }
        } else if (punchedOut) {
          // Already punched out → also force back to Attendance page
          if (!currentPath.includes('/attendances')) {
            window.location.href = attendancePath;
            return;
          }
        }
      }
    })
    .catch(() => {
      // ❌ Token invalid → clear and show login form
      localStorage.clear();
      document.getElementById('loginBox').style.display = 'block';
    });
} else {
  // ❌ No token → show login form
  document.getElementById('loginBox').style.display = 'block';
}


  // Login form submit
  document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    axios.post("/login", {
        loginField: document.getElementById('email').value,
        password: document.getElementById('password').value
    })
    .then(res => {
        const token = res.data.token;
        const user  = res.data.user;
        const roles = res.data.roles;
        const permissions = res.data.permissions;

        // Save to localStorage
        localStorage.setItem('api_token', token);
        localStorage.setItem('user', JSON.stringify(user));
        localStorage.setItem('roles', JSON.stringify(roles));
        localStorage.setItem('permissions', JSON.stringify(permissions));

        // Redirect to dashboard
        window.location.href = "{{ route('dashboard') }}";
    })
    .catch(err => {
        console.error(err);
        alert(err.response?.data?.message || "Login failed");
    });
  });
</script>

@livewireScripts
</body>
</html>
