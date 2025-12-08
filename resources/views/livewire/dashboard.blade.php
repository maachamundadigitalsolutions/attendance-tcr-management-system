<div class="container mt-4">
    <h1>Welcome , <span id="username"></span></h1>
    <p>Roles: <span id="roles"></span></p>
    <p>Permissions: <span id="permissions"></span></p>
    <!-- <button onclick="logout()" class="btn btn-danger mt-3">Logout</button> -->
</div>

@push('scripts')

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
    
    // ✅ Put your formatPermission function here
    function formatPermission(p) {
      return (p.label || p.name)
        .replace(/-/g, ' ')
        .replace(/\b\w/g, c => c.toUpperCase());
    }


    api.get('/me')
      .then(res => {
        document.getElementById('username').innerText = res.data.user?.name;
        document.getElementById('roles').innerText = (res.data.roles || []).join(', ');
       
        
          // ✅ Use it here
          const perms = (res.data.permissions || []).map(formatPermission);
           console.log('perms', res.data.permissions);
          document.getElementById('permissions').innerText = perms.join(', ');

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

</script>
@endpush
