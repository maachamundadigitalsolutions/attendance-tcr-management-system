<div>
    <h1>Attendance Management</h1>

    {{-- Employee Form --}}
    <div id="attendanceFormBlock" style="display:none;">
      <button class="btn btn-primary mb-3" onclick="openCreateModal()">Mark Attendance</button>
    </div>

    {{-- Admin / Employee List --}}
    <div id="attendanceListBlock" style="display:none;" class="mt-4">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th><th>User</th><th>Date</th><th>Status</th><th>Remarks</th><th>Photo</th><th>Actions</th>
          </tr>
        </thead>
        <tbody id="attendance-table"></tbody>
      </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="attendanceModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form id="attendanceForm">
            <div class="modal-header">
              <h5 class="modal-title">Attendance</h5>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <input type="hidden" id="editId">
              <div class="form-group">
                <label>Status</label>
                <select id="status" class="form-control">
                  <option value="present">Present</option>
                  <option value="absent">Absent</option>
                  <option value="leave">Leave</option>
                </select>
              </div>
              <div class="form-group">
                <label>Remarks</label>
                <input type="text" id="remarks" class="form-control">
              </div>
              <div class="form-group">
                <label>Selfie Photo</label>
                <input type="file" id="photo" class="form-control" accept="image/*">
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

let attendanceInitialized = false;

function initAttendance() {
  if (attendanceInitialized) return;   // 👈 prevent re-run
  attendanceInitialized = true;

    // ✅ only run on Attendances pages
    if (!window.location.pathname.includes('attendances')) {
      return;
    }
        
  axios.get('/me')
    .then(res => {
      const perms = res.data.permissions || [];
      const formBlock = document.getElementById('attendanceFormBlock');
      if (formBlock && perms.includes('attendance-mark')) {
        formBlock.style.display = 'block';
      }

      const listBlock = document.getElementById('attendanceListBlock');
      if (listBlock && (perms.includes('attendance-view-all') || perms.includes('attendance-mark'))) {
        listBlock.style.display = 'block';
        loadAttendances(perms);
      }
    })
    .catch(err => console.error("Error fetching user data:", err));
}


// 👇 Bind only once
if (window.location.pathname.includes('attendances-management')) {
  document.addEventListener("DOMContentLoaded", initAttendance);
  document.addEventListener("livewire:navigated", () => {
    if (!attendanceInitialized) initAttendance();
  });
}


// ✅ Load attendances
function loadAttendances(userPerms = []) {
  console.log("Calling GET /attendances ...");
  axios.get('/attendances')
    .then(res => {
      console.log("Response:", res);

      const tbody = document.getElementById('attendance-table');
      if (!tbody) return;

      tbody.innerHTML = '';
      res.data.data.forEach(a => {
        tbody.innerHTML += `
          <tr>
            <td>${a.id}</td>
            <td>${a.user?.name ?? a.user_id}</td>
            <td>${a.date}</td>
            <td>${a.status}</td>
            <td>${a.remarks ?? ''}</td>
            <td>${a.photo_path ? `<img src="/storage/${a.photo_path}" width="60">` : 'No Photo'}</td>
            <td>
              ${userPerms.includes('attendance-delete')
                ? `<button class="btn btn-sm btn-danger" onclick="deleteAttendance(${a.id})">Delete</button>`
                : ''}
            </td>
          </tr>`;
      });
    })
    .catch(err => console.error("Error loading attendances:", err));
}

// ✅ Save attendance
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById('attendanceForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData();
      formData.append('status', document.getElementById('status').value);
      formData.append('remarks', document.getElementById('remarks').value);
      const photo = document.getElementById('photo').files[0];
      if (photo) formData.append('photo', photo);

      axios.post('/attendances', formData)
        .then(() => {
          $('#attendanceModal').modal('hide');
          loadAttendances();
        })
        .catch(err => alert(err.response?.data?.message || 'Error saving attendance'));
    });
  }
});

// ✅ Delete attendance
window.deleteAttendance = function(id) {
  if (confirm("Delete this attendance?")) {
    axios.delete(`/attendances/${id}`)
      .then(() => loadAttendances())
      .catch(err => console.error("Error deleting attendance:", err));
  }
}

// ✅ Open modal
window.openCreateModal = function() {
  document.getElementById('editId').value = '';
  document.getElementById('status').value = 'present';
  document.getElementById('remarks').value = '';
  document.getElementById('photo').value = '';
  $('#attendanceModal').modal('show');
}
</script>
@endscript
