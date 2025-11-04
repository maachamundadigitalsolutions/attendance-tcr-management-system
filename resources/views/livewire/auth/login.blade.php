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

<div class="login-box">
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
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
// document.addEventListener("DOMContentLoaded", function() {
//     const token = localStorage.getItem('api_token');
//     if (token) {
//         axios.get("http://127.0.0.1:8000/api/v1/user", {
//             headers: {
//                 "Authorization": "Bearer " + token,
//                 "Accept": "application/json"
//             }
//         })
//         .then(res => {
//             // Token valid → redirect to dashboard
//             window.location.href = "{{ route('dashboard') }}";
//         })
//         .catch(err => {
//             // Token invalid → clear and stay on login
//             localStorage.removeItem('api_token');
//             localStorage.removeItem('user');
//         });
//     }
// });

// Existing loginForm submit code niche j rahe
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    axios.post("http://127.0.0.1:8000/api/v1/login", {
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

        console.log("Login success, redirecting...");
        console.log("Saved token:", localStorage.getItem('api_token'));

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
