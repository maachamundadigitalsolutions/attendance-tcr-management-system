<div class="container mt-4">
    <h1>Welcome, <span id="username"></span></h1>
    <p>Roles: <span id="roles"></span></p>
    <p>Permissions: <span id="permissions"></span></p>
    <button onclick="logout()" class="btn btn-danger mt-3">Logout</button>
</div>

@push('scripts')
<script src="/js/axios.min.js"></script>
<script src="{{ asset('js/axios-setup.js') }}"></script>

<script>
  
function loadUserData() {
    // ✅ only run on Dashboard pages
    if (!window.location.pathname.includes('dashboard')) {
      return;
    }

    const token = localStorage.getItem('api_token');
    if (!token) {
        window.location.href = "/login";
        return;
    }


    axios.get('/me')
      .then(res => {
        console.log('dashboard me', res);
        
        document.getElementById('username').innerText = res.data.user?.name;
        document.getElementById('roles').innerText = res.data.roles.join(', ');
        document.getElementById('permissions').innerText = res.data.permissions.join(', ');
      })
      .catch(err => {
        console.error("Error fetching user data:", err);
        if (err.response?.status === 401) {
          alert("Login expired, please login again.");
          // localStorage.clear();
          // window.location.href = "/login";
        }
      });
    
      
}

// ✅ Run on first load
document.addEventListener("DOMContentLoaded", () => {
    if (window.location.pathname.includes('dashboard')) {
        loadUserData();
    }
});

// ✅ Run again when Livewire navigates (SPA navigation)
document.addEventListener("livewire:navigated", () => {
    if (window.location.pathname.includes('dashboard')) {
        loadUserData();
    }
});

function logout() {
    axios.post('/logout')
      .then(() => {
        localStorage.clear();
        window.location.href = "/login";
      })
      .catch(err => {
        console.error("Logout failed:", err);
      });
}
</script>
@endpush
