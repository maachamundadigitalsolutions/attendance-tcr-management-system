<div>
  <h1>User Management</h1>
  <button class="btn btn-primary mb-3" onclick="openCreateModal()">Add User</button>

  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Users</h3>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="usersTable" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>User ID</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="userModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form id="userForm">
          <div class="modal-header">
            <h5 class="modal-title">User</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="editId">

            <div class="form-group">
              <label>Name</label>
              <input type="text" id="name" class="form-control" required>
            </div>

            <div class="form-group">
              <label>User ID</label>
              <input type="text" id="user_id" class="form-control" required>
            </div>

            <div class="form-group" id="password-group">
              <label>Password</label>
              <input type="password" id="password" class="form-control">
            </div>

            <div class="form-group">
              <label>Role</label>
              <select id="role" class="form-control" required>
                <option value="">-- Select Role --</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Save</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function () {
  let usersTable;

  // 👉 Load roles dropdown
  function loadRolesDropdown(selectedId = '') {
    axios.get('/roles').then(res => {
      const select = document.getElementById('role');
      if (select) {
        select.innerHTML = '<option value="">-- Select Role --</option>';
        (res.data.roles || []).forEach(r => {
          select.insertAdjacentHTML('beforeend',
            `<option value="${r.id}">${r.name}</option>`);
        });
        if (selectedId) {
          select.value = selectedId;
        }
      }
    }).catch(err => console.error("Error loading roles:", err));
  }

  // 👉 Open create modal
  window.openCreateModal = function() {
    loadRolesDropdown();
    document.getElementById('editId').value = '';
    document.getElementById('name').value = '';
    document.getElementById('user_id').value = '';
    document.getElementById('password').value = '';
    $('#password-group').show();
    $('#userModal').modal('show');
  };

  // 👉 Edit user
  window.editUser = function(id) {
    axios.get(`/users/${id}`).then(res => {
    console.log('Failed to load user',res.data );
    
    const u = res.data.data || res.data; // handle both cases

    // if roles not present, fallback to empty string
    const roleId = (u.roles && u.roles.length > 0) ? u.roles[0].id : '';
    loadRolesDropdown(roleId);

      document.getElementById('editId').value = u.id;
      document.getElementById('name').value = u.name;
      document.getElementById('user_id').value = u.user_id;
      document.getElementById('password').value = '';
      $('#password-group').hide();
      $('#userModal').modal('show');
    }).catch(err => {
      console.error("Error loading user:", err);
      alert("Failed to load user");
    });
  };

  // 👉 Delete user
  window.deleteUser = function(id) {
    if (confirm("Delete this user?")) {
      axios.delete(`/users/${id}`).then(() => {
        reloadUsers();
      }).catch(err => {
        console.error("Delete failed:", err);
        alert("Delete failed");
      });
    }
  };

  // 👉 Initialize DataTable
  function initUsersTable() {
    if ($.fn.DataTable.isDataTable('#usersTable')) {
      $('#usersTable').DataTable().clear().destroy();
    }

    usersTable = $("#usersTable").DataTable({
      responsive: true,
      lengthChange: true,
      autoWidth: false,
      paging: true,
      searching: true,
      ordering: true,
      info: true,
      pageLength: 10,
      buttons: [
        { extend: 'copy', className: 'btn btn-secondary btn-sm' },
        { extend: 'csv', className: 'btn btn-info btn-sm' },
        { extend: 'excel', className: 'btn btn-success btn-sm', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'pdf', className: 'btn btn-danger btn-sm', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'print', className: 'btn btn-primary btn-sm', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'colvis', className: 'btn btn-warning btn-sm' }
      ]
    });
    usersTable.buttons().container().appendTo('#usersTable_wrapper .col-md-6:eq(0)');

    reloadUsers();
  }

  // 👉 Reload users
  function reloadUsers() {
    axios.get('/user-list')
      .then(res => {
        usersTable.clear();
        (res.data?.data || []).forEach(u => {
          const roleName = u.roles && u.roles.length ? u.roles[0].name : '';
          usersTable.row.add([
            u.id,
            u.name,
            u.user_id,
            roleName,
            `
              <button class="btn btn-sm btn-info editBtn" data-id="${u.id}">Edit</button>
              <button class="btn btn-sm btn-danger deleteBtn" data-id="${u.id}">Delete</button>
            `
          ]);
        });
        usersTable.draw(false);

        // bind events
        $('#usersTable').off('click', '.editBtn').on('click', '.editBtn', function () {
          const id = $(this).data('id');
          editUser(id);
        });

        $('#usersTable').off('click', '.deleteBtn').on('click', '.deleteBtn', function () {
          const id = $(this).data('id');
          deleteUser(id);
        });
      })
      .catch(err => console.error("Error fetching users:", err));
  }

  function showToast(icon, title) {
  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: icon,
    title: title,
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true
  });
}


  // 👉 Form submit
  document.addEventListener('submit', function(e) {
    if (e.target && e.target.id === 'userForm') {
      e.preventDefault();
      const id = document.getElementById('editId').value;      
      const payload = {
        name: document.getElementById('name').value,
        user_id: document.getElementById('user_id').value,
        password: document.getElementById('password').value,
        role: document.getElementById('role').value
      };

      const request = id
        ? axios.put(`/users/${id}`, payload)
        : axios.post('/users', payload);

      request.then(() => {
        $('#userModal').modal('hide');
        reloadUsers();
      }).catch(err => {
          if (err.response && err.response.data.errors) {
            const errors = err.response.data.errors;
            Object.keys(errors).forEach(field => {
              errors[field].forEach(msg => {
                Swal.fire({
                  toast: true,
                  position: 'top-end',
                  icon: 'error',
                  title: msg,
                  showConfirmButton: false,
                  timer: 4000,
                  timerProgressBar: true
                });
              });
            });
          } else {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'error',
              title: 'Something went wrong',
              showConfirmButton: false,
              timer: 4000,
              timerProgressBar: true
            });
          }
        });
    }
  });

  document.addEventListener('DOMContentLoaded', initUsersTable);
  document.addEventListener('livewire:navigated', initUsersTable);

})();
</script>
@endpush
