<div>
    <!-- Card -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Roles & Permissions</h3>
        <button class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#roleModal">
          Create Role
        </button>
      </div>
      <div class="card-body">
        <table id="rolesTable" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Role</th>
              <th>Permissions</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <form id="roleForm">
            <div class="modal-header">
              <h5 class="modal-title">Create / Edit Role</h5>
              <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
              <input type="hidden" id="role_id" name="role_id">
              <div class="form-group">
                <label>Role Name</label>
                <input type="text" id="role_name" name="name" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Select Permissions</label>
                <div id="permissionsList"></div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Save</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>

@push('scripts')
<script>
function initRolesTable() {
  const table = $("#rolesTable").DataTable({
    destroy: true, // allow re-init when Livewire reloads
    responsive: true,
    autoWidth: false,
    buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"]
  });
  table.buttons().container().appendTo('#rolesTable_wrapper .col-md-6:eq(0)');

  // ✅ Load roles and build permissions list
  axios.get('/roles').then(res => {
    console.log('resssss', res);
    
    table.clear();
    res.data.roles.forEach(r => {
      table.row.add([
        r.id,
        r.name,
        r.permissions.join(', '),
        `<button class="btn btn-info btn-sm editBtn" data-id="${r.id}">Edit</button>
         <button class="btn btn-danger btn-sm deleteBtn" data-id="${r.id}">Delete</button>`
      ]);
    });
    table.draw(false);

    // Build unique permissions list
    const allPerms = [...new Set(res.data.roles.flatMap(r => r.permissions))];
    const container = document.getElementById('permissionsList');
    container.innerHTML = '';
    allPerms.forEach(p => {
      container.insertAdjacentHTML('beforeend', `
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="permissions[]" value="${p}" id="perm_${p}">
          <label class="form-check-label" for="perm_${p}">${p}</label>
        </div>
      `);
    });
  });

  // ✅ Handle form submit
  document.getElementById('roleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const payload = {
      name: formData.get('name'),
      permissions: formData.getAll('permissions[]')
    };
    const roleId = formData.get('role_id');

    if (roleId) {
      axios.put(`/roles/${roleId}`, payload)
        .then(() => Livewire.emit('refreshComponent'))
        .catch(err => {
          if (err.response && err.response.data.errors) {
            alert(err.response.data.errors.name[0]);
          }
        });
    } else {
      axios.post('/roles', payload)
        .then(() => Livewire.emit('refreshComponent'))
        .catch(err => {
          if (err.response && err.response.data.errors) {
            alert(err.response.data.errors.name[0]);
          }
        });
    }
  });

  // ✅ Edit role
  $('#rolesTable').on('click', '.editBtn', function () {
    const id = $(this).data('id');
    axios.get(`/roles/${id}`).then(res => {
      const role = res.data.role;
      document.getElementById('role_id').value = role.id;
      document.getElementById('role_name').value = role.name;

      document.querySelectorAll('#permissionsList input[type=checkbox]').forEach(cb => cb.checked = false);
      role.permissions.forEach(perm => {
        const checkbox = document.querySelector(`[value="${perm}"]`);
        if (checkbox) checkbox.checked = true;
      });

      $('#roleModal').modal('show');
    });
  });

  // ✅ Delete role
  $('#rolesTable').on('click', '.deleteBtn', function () {
    const id = $(this).data('id');
    if (confirm("Delete this role?")) {
      axios.delete(`/roles/${id}`).then(() => Livewire.emit('refreshComponent'));
    }
  });
}

// ✅ Livewire hook
document.addEventListener("livewire:load", () => {
  initRolesTable();
});

// Optional: re-init when Livewire component refreshes
Livewire.on('refreshComponent', () => {
  initRolesTable();
});
</script>
@endpush
