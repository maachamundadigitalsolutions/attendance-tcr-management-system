<div class="container mt-4">
  <h3>User Management</h3>

  <!-- wire:ignore prevents Livewire from overwriting this table -->
  <table class="table table-bordered mt-3" wire:ignore>
    <thead>
      <tr><th>Name</th><th>User ID</th></tr>
    </thead>
    <tbody id="users-table"></tbody>
  </table>
</div>
<!-- 
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('js/axios-setup.js') }}"></script> -->

<script>
(function() {
  let isLoadingUsers = false;

  function loadUsers() {
    if (isLoadingUsers) return;   // ✅ prevent duplicate calls
    isLoadingUsers = true;

    axios.get('/user-list')
      .then(res => {
        const tbody = document.getElementById('users-table');
        if (tbody) {
          tbody.innerHTML = '';
          (res.data || []).forEach(user => {
            tbody.innerHTML += `<tr>
              <td>${user.name}</td>
              <td>${user.user_id}</td>
            </tr>`;
          });
        }
      })
      .catch(err => console.error("Error fetching users:", err))
      .finally(() => {
        isLoadingUsers = false;
      });
  }

  // ✅ Run once on first page load
  // document.addEventListener("DOMContentLoaded", () => {
  //   if (window.location.pathname.includes('user-management')) {
  //     loadUsers();
  //   }
  // });

  // ✅ Run again when Livewire navigates (SPA navigation)
  document.addEventListener("livewire:navigated", () => {
    if (window.location.pathname.includes('user-management')) {
      loadUsers();
    }
  });
})();
</script>
