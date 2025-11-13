<div class="container mt-4">
  <h3>Roles & Permissions</h3>
  <table class="table table-bordered">
    <thead>
      <tr>
      <th>Role Name</th>
      <th>Permissions</th>
    </tr>
    </thead>
    <tbody id="roles-table"></tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('js/axios-setup.js') }}"></script>
<script>

 function loadRoles() {
  const tbody = document.getElementById('roles-table');
  if (!tbody) return;

  axios.get('/roles')
    .then(res => {
      tbody.innerHTML = '';
      (res.data.roles || []).forEach(role => {
        tbody.innerHTML += `<tr>
          <td>${role.name}</td>
          <td>${role.permissions}</td>
        </tr>`;
      });
    })
    .catch(err => console.error("Error fetching roles:", err));
   
}

  // document.addEventListener("DOMContentLoaded", ()=>{
  //    if (window.location.pathname.includes('roles-permissions')) {
  //     loadRoles();
  //   }
  // });

  document.addEventListener("livewire:navigated", () => {
    // if (window.location.pathname.includes('roles-permissions')) {
      loadRoles();
    // }
  }, { once: true });
</script>>



