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
              <input type="text" id="name" class="form-control">
            </div>
            <div class="form-group">
              <label>User ID</label>
              <input type="text" id="user_id" class="form-control">
            </div>
            <div class="form-group">
              <label>Password</label>
              <input type="password" id="password" class="form-control">
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

@script
<script>
  // Load users when navigated
  document.addEventListener('livewire:navigated', loadUsers);

  function loadUsers() {
    const tbody = document.getElementById('users-table');
    if (!tbody) return; // safety check

    axios.get('/user-list').then(res => {
      tbody.innerHTML = '';
      res.data.data.forEach(u => {
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
    })
    .catch(err => console.error("Error fetching roles:", err));
  }

  // ✅ Define function globally so button works
  window.openCreateModal = function() {
    document.getElementById('editId').value = '';
    document.getElementById('name').value = '';
    document.getElementById('user_id').value = '';
    document.getElementById('password').value = '';
    $('#userModal').modal('show');
  }

  window.editUser = function(id) {
    axios.get(`/users/${id}`).then(res => {
      const u = res.data;
      document.getElementById('editId').value = u.id;
      document.getElementById('name').value = u.name;
      document.getElementById('user_id').value = u.user_id;
      document.getElementById('password').value = '';
      $('#userModal').modal('show');
    })
    .catch(err => console.error("Error fetching roles:", err));
  }

  document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('editId').value;
    const payload = {
      name: document.getElementById('name').value,
      user_id: document.getElementById('user_id').value,
      password: document.getElementById('password').value
    };

    if (id) {
      axios.put(`/users/${id}`, payload).then(() => {
        $('#userModal').modal('hide');
        loadUsers();
      });
    } else {
      axios.post('/users', payload).then(() => {
        $('#userModal').modal('hide');
        loadUsers();
      });
    }
  });

  window.deleteUser = function(id) {
    if (confirm("Delete this user?")) {
      axios.delete(`/users/${id}`).then(() => loadUsers());
    }
  }
</script>
@endscript
