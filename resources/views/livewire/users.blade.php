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
              <th>User ID</th>
              <th>Role</th>
              <th>Name</th>
              <th>address</th>
              <th>Phone</th>
              <th>Email</th>
              <th>DOB</th>
              <th>DOJ</th>
              <th>Shirt Size</th>
              <th>Tshirt Size</th>
              <th>Trouser Size</th>
              <th>Jeans_size</th>
              <th>Education</th>
              <th>summary_exp</th>
              <th>Total Exp</th>
              <th>Emergency Contact.</th>
              <th>Product</th>
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

            <div class="form-group">
              <label>Address</label>
              <textarea id="address" class="form-control"></textarea>
            </div>

            <div class="form-group">
              <label>Phone</label>
              <input type="text" id="phone" class="form-control">
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="email" id="email" class="form-control">
            </div>

            <div class="form-group">
              <label>Shirt Size</label>
              <input type="text" id="shirt_size" class="form-control">
            </div>

            <div class="form-group">
              <label>T-Shirt Size</label>
              <input type="text" id="tshirt_size" class="form-control">
            </div>

            <div class="form-group">
              <label>Trouser Size</label>
              <input type="text" id="trouser_size" class="form-control">
            </div>

            <div class="form-group">
              <label>Jeans Size</label>
              <input type="text" id="jeans_size" class="form-control">
            </div>

            <div class="form-group">
              <label>Date of Birth</label>
              <input type="date" id="dob" class="form-control">
            </div>

            <div class="form-group">
              <label>Date of Joining</label>
              <input type="date" id="doj" class="form-control">
            </div>

            <div class="form-group">
              <label>Total Experience</label>
              <input type="text" id="total_exp" class="form-control">
            </div>

            <div class="form-group">
              <label>Summary Experience</label>
              <input type="text" id="summary_exp" class="form-control">
            </div>

            <div class="form-group">
              <label>Education</label>
              <input type="text" id="education" class="form-control">
            </div>

            <div class="form-group">
              <label>Emergency Contact No.</label>
              <input type="text" id="emergency_contact" class="form-control">
            </div>

            <div class="form-group">
              <label>Product</label>
              <input type="text" id="product" class="form-control">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    document.getElementById('user_id').value = '';
    document.getElementById('name').value = '';
    document.getElementById('address').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('email').value = '';
    document.getElementById('shirt_size').value = '';
    document.getElementById('tshirt_size').value = '';
    document.getElementById('trouser_size').value = '';
    document.getElementById('jeans_size').value = '';
    document.getElementById('dob').value = '';
    document.getElementById('doj').value = '';
    document.getElementById('education').value = '';
    document.getElementById('total_exp').value = '';
    document.getElementById('summary_exp').value = '';
    document.getElementById('emergency_contact').value = '';
    document.getElementById('product').value = '';
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
      document.getElementById('phone').value = u.phone ?? '';
      document.getElementById('email').value = u.email ?? '';
      document.getElementById('dob').value = u.dob;
      document.getElementById('doj').value = u.doj;
      document.getElementById('total_exp').value = u.total_exp ?? '';
      document.getElementById('education').value = u.education ?? '';
      document.getElementById('product').value = u.product ?? '';
      
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
      $('#usersTable').DataTable().destroy();
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
      dom: 'Bfrtip',
      buttons: [
        { extend: 'copy', className: 'btn btn-secondary btn-sm' },
        { extend: 'csv', className: 'btn btn-info btn-sm' },
        { extend: 'excel', className: 'btn btn-success btn-sm', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'pdf', className: 'btn btn-danger btn-sm', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'print', className: 'btn btn-primary btn-sm', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'colvis', className: 'btn btn-warning btn-sm' }
      ]
    });

    // check wrapper exists before append
    if ($('#usersTable_wrapper .col-md-6:eq(0)').length) {
      usersTable.buttons().container().appendTo('#usersTable_wrapper .col-md-6:eq(0)');
    }

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
              u.user_id,
              roleName,
              u.name,
              u.address || '-',
              u.phone || '-',
              u.email || '-',
              u.dob || '-',
              u.doj || '-',
              u.shirt_size || '-',
              u.tshirt_size || '-',
              u.trouser_size || '-',
              u.jeans_size || '-',
             
              u.education || '-',
              u.summary_exp || '-',
              u.total_exp || '-',
              u.emergency_contact || '-',
              u.product || '-',
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
     console.log('testsgdgsd22222');
    if (e.target && e.target.id === 'userForm') {
      e.preventDefault();
           
      const id = document.getElementById('editId').value;      
      const payload = {
          name: document.getElementById('name').value,
          user_id: document.getElementById('user_id').value,
          password: document.getElementById('password').value,
          role: document.getElementById('role').value,
          address: document.getElementById('address').value,
          phone: document.getElementById('phone').value,
          email: document.getElementById('email').value,
          shirt_size: document.getElementById('shirt_size').value,
          tshirt_size: document.getElementById('tshirt_size').value,
          trouser_size: document.getElementById('trouser_size').value,
          jeans_size: document.getElementById('jeans_size').value,
          dob: document.getElementById('dob').value,
          doj: document.getElementById('doj').value,
          total_exp: document.getElementById('total_exp').value,
          summary_exp: document.getElementById('summary_exp').value,
          education: document.getElementById('education').value,
          emergency_contact: document.getElementById('emergency_contact').value,
          product: document.getElementById('product').value
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
  // document.addEventListener('livewire:navigated', initUsersTable);
  document.addEventListener('livewire:navigated', () => {
  setTimeout(initUsersTable, 100);
});


})();
</script>
@endpush
