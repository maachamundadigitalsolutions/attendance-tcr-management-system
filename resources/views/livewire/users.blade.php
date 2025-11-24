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
              <input type="text" id="name" class="form-control" autocomplete="name">
            </div>

            <div class="form-group">
              <label>User ID</label>
              <input type="text" id="user_id" class="form-control" autocomplete="username">
            </div>

            <div class="form-group" id="password-group">
              <label>Password</label>
              <input type="password" id="password" class="form-control" autocomplete="new-password">
            </div>

            <div class="form-group">
              <label>Role</label>
              <select id="role" class="form-control">
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
  function initUsersTable() {
    // Destroy old DataTable if exists
    if ($.fn.DataTable.isDataTable('#usersTable')) {
      $('#usersTable').DataTable().clear().destroy();
    }

    const table = $("#usersTable").DataTable({
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
    table.buttons().container().appendTo('#usersTable_wrapper .col-md-6:eq(0)');

    // Load users
    axios.get('/user-list')
      .then(res => {
        table.clear();
        (res.data?.data || []).forEach(u => {
          const roleName = u.roles && u.roles.length ? u.roles[0].name : '';
          table.row.add([
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
        table.draw(false);
      })
      .catch(err => console.error("Error fetching users:", err));

    // Edit user
    $('#usersTable').off('click', '.editBtn').on('click', '.editBtn', function () {
      const id = $(this).data('id');
      editUser(id);
    });

    // Delete user
    $('#usersTable').off('click', '.deleteBtn').on('click', '.deleteBtn', function () {
      const id = $(this).data('id');
      deleteUser(id);
    });
  }

  document.addEventListener('DOMContentLoaded', initUsersTable);
  document.addEventListener('livewire:navigated', initUsersTable);
})();
</script>
@endpush
