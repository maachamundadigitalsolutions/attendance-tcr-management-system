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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function () {
  var rolesTable = null;

  function initRolesTable() {
    if (!window.location.pathname.includes('roles-permissions')) return;

    if (!$.fn.DataTable.isDataTable('#rolesTable')) {
      rolesTable = $("#rolesTable").DataTable({
        responsive: true,
        autoWidth: false,
        buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"]
      });
      rolesTable.buttons().container().appendTo('#rolesTable_wrapper .col-md-6:eq(0)');
    }

    reloadRoles();
  }

  function reloadRoles() {
    api.get('/roles').then(res => {
      rolesTable.clear();
      res.data.roles.forEach(r => {
        rolesTable.row.add([
          r.id,
          r.name,
          r.permissions.map(p => `<span class="badge badge-info mr-1">${p.label}</span>`).join(''),
          `<button class="btn btn-info btn-sm editBtn" data-id="${r.id}">Edit</button>
           <button class="btn btn-danger btn-sm deleteBtn" data-id="${r.id}">Delete</button>`
        ]);
      });
      rolesTable.draw(false);

      // build permissions list
      const allPerms = [...new Set(res.data.all_permissions)];
      const container = document.getElementById('permissionsList');
      if (container) {
        container.innerHTML = '';
        allPerms.forEach(p => {
          const value = p.name || p;
          const label = p.label || p;
          container.insertAdjacentHTML('beforeend', `
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="permissions[]" value="${value}" id="perm_${value}">
              <label class="form-check-label" for="perm_${value}">${label}</label>
            </div>
          `);
        });
      }
    });
  }

  // handle form submit
  document.addEventListener('submit', function(e) {
    if (e.target && e.target.id === 'roleForm') {
      e.preventDefault();
      const formData = new FormData(e.target);
      const payload = {
        name: formData.get('name'),
        permissions: formData.getAll('permissions[]')
      };
      const roleId = formData.get('role_id');

      const request = roleId
        ? api.put(`/roles/${roleId}`, payload)
        : api.post('/roles', payload);

      request.then(() => {
        $('#roleModal').modal('hide');
        reloadRoles();
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: roleId ? 'Role updated successfully!' : 'Role created successfully!',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
      }).catch(err => {
        console.error('Role save error:', err);
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: 'Failed to save role',
          showConfirmButton: false,
          timer: 4000,
          timerProgressBar: true
        });
      });
    }
  });

  // Edit role
  $('#rolesTable').on('click', '.editBtn', function () {
    const id = $(this).data('id');
    api.get(`/roles/${id}`).then(res => {
      const role = res.data.role;
      document.getElementById('role_id').value = role.id;
      document.getElementById('role_name').value = role.name;
      document.querySelectorAll('#permissionsList input[type=checkbox]').forEach(cb => cb.checked = false);
      role.permissions.forEach(perm => {
          const checkbox = document.querySelector(`[value="${perm.name || perm}"]`);
          if (checkbox) checkbox.checked = true;
        });

      $('#roleModal').modal('show');
    });
  });

  // Delete role with SweetAlert confirm
  $('#rolesTable').on('click', '.deleteBtn', function () {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Are you sure?',
      text: "This will delete the role permanently",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        api.delete(`/roles/${id}`).then(() => {
          reloadRoles();
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Role deleted successfully!',
            showConfirmButton: false,
            timer: 3000
          });
        });
      }
    });
  });

  // Reset form when modal closes
  $('#roleModal').on('hidden.bs.modal', function () {
    document.getElementById('roleForm').reset();
    document.getElementById('role_id').value = '';
    document.querySelectorAll('#permissionsList input[type=checkbox]').forEach(cb => cb.checked = false);
  });

  // Hooks
  document.addEventListener("livewire:load", initRolesTable);
  document.addEventListener('livewire:navigated', initRolesTable);
  if (window.Livewire) {
    Livewire.on('refreshComponent', reloadRoles);
  }
})();
</script>
@endpush
