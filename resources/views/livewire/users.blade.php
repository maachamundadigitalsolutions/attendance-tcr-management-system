<div>
  <h1>User Management</h1>
  <button class="btn btn-primary mb-3" onclick="openCreateModal()">Add User</button>

  <table class="table table-bordered">
    <thead>
      <tr><th>ID</th><th>Name</th><th>User ID</th><th>Actions</th></tr>
    </thead>
    <tbody id="users-table"></tbody>
  </table>

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
            <div class="form-group">
              <label>Password</label>
              <input type="password" id="password" class="form-control" autocomplete="current-password">
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
  function loadUsers() {
    const tbody = document.getElementById('users-table');
    if (!tbody) return;

    axios.get('/user-list').then(res => {
      tbody.innerHTML = '';
      (res.data?.data || []).forEach(u => {
        tbody.innerHTML += `
          <tr>
            <td>${u.id}</td>
            <td>${u.name}</td>
            <td>${u.user_id}</td>
            <td>
              <button class="btn btn-sm btn-info" onclick="editUser(${u.id})">Edit</button>
              <button class="btn btn-sm btn-danger" onclick="deleteUser(${u.id})">Delete</button>
            </td>
          </tr>`;
      });
    }).catch(err => console.error("Error fetching users:", err));
  }

  window.openCreateModal = function() {
    ['editId','name','user_id','password'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
    $('#userModal').modal('show');
  }

  window.editUser = function(id) {
    axios.get(`/users/${id}`).then(res => {
      const u = res.data;
      if (!u) return;
      document.getElementById('editId').value = u.id;
      document.getElementById('name').value = u.name;
      document.getElementById('user_id').value = u.user_id;
      document.getElementById('password').value = '';
      $('#userModal').modal('show');
    }).catch(err => console.error("Error fetching user:", err));
  }

  document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('editId').value;
    const payload = {
      name: document.getElementById('name').value,
      user_id: document.getElementById('user_id').value,
      password: document.getElementById('password').value
    };

    const request = id ? axios.put(`/users/${id}`, payload) : axios.post('/users', payload);

    request.then(() => {
      $('#userModal').modal('hide');
      loadUsers();
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: id ? 'User updated successfully!' : 'User created successfully!',
        showConfirmButton: false,
        timer: 3000
      });
    }).catch(err => console.error("Error saving user:", err));
  });

  window.deleteUser = function(id) {
    if (confirm("Delete this user?")) {
      axios.delete(`/users/${id}`).then(() => {
        loadUsers();
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'User deleted successfully!',
          showConfirmButton: false,
          timer: 3000
        });
      });
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    if (window.location.pathname.includes('user-management')) {
      loadUsers();
    }
  });

  document.addEventListener('livewire:navigated', () => {
    if (window.location.pathname.includes('user-management')) {
      loadUsers();
    }
  });
</script>
@endpush
