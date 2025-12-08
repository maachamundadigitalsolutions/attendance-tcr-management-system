<div>
    <h1>Attendance Management</h1>

    {{-- Employee Form --}}
    <div id="attendanceFormBlock" style="display:none;">
      <button class="btn btn-primary mb-3" onclick="openCreateModal()">Mark Attendance</button>
    </div>

    {{-- Admin / Employee List --}}
    <div id="attendanceListBlock" style="display:none;" class="mt-4">
      <table id="attendanceTable" class="table table-bordered table-striped">
       <thead>
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Date</th>
            <th>In Time</th>
            <th>In Photo</th>
            <th>Out Time</th>
            <th>Out Photo</th>
            <th>Status</th>
            <th>Actions</th>
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

              <!-- In Photo -->
              <div class="form-group" id="inPhotoBlock">
                <label>In Photo</label>
                <input type="file" id="in_photo" name="in_photo" class="form-control" accept="image/*" capture="camera">
              </div>

              <!-- Out Photo -->
              <div class="form-group" id="outPhotoBlock" style="display:none;">
                <label>Out Photo</label>
                <input type="file" id="out_photo" name="out_photo" class="form-control" accept="image/*" capture="camera">
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
  let attendanceInitialized = false;
  let authUserId = null;

  function initAttendance() {
    if (attendanceInitialized) return;
    attendanceInitialized = true;

    if (!window.location.pathname.includes('attendances')) return;

    // Get current user info
    api.get('/me')
      .then(res => {
        authUserId = res.data.user.id;        
        perms = (res.data.permissions || []).map(p => p.name);
        console.log('perms', perms);
        
        if (perms.includes('attendance-view-all') || perms.includes('attendance-mark')) {
          document.getElementById('attendanceListBlock').style.display = 'block';
          loadAttendances(perms);
        }
      })
      .catch(err => console.error("Error fetching user data:", err));
  }

  function checkPunchStatus(attendances) {
    const today = dayjs().format('DD-MM-YYYY'); // always ISO format
    const record = attendances.find(a => {
      return a?.date === today && a.user_id === authUserId;
    });

    const block = document.getElementById('attendanceFormBlock');
    block.style.display = 'block';

    if (!record) {
      // No record → Punch In
      block.innerHTML = `<button class="btn btn-primary mb-3" onclick="openCreateModal()">Punch In</button>`;
    } else if (record && !record.time_out) {
      // Already punched in but not out → Punch Out
      block.innerHTML = `<button class="btn btn-warning mb-3" onclick="punchOut(${record.id})">Punch Out</button>`;
    } else {
      // Already punched in and out → Completed
      block.innerHTML = `<span class="badge badge-success">Attendance Completed</span>`;
    }
  }



  function loadAttendances(userPerms = []) {
    api.get('/attendances')
      .then(res => {
        const attendances = res.data.data;
        const tbody = document.getElementById('attendance-table');
        if (!tbody) return;

        tbody.innerHTML = '';
        attendances.forEach(a => {
          tbody.innerHTML += `
            <tr>
              <td>${a.id}</td>
              <td>${a.user?.name ?? a.user_id}</td>
              <td>${a.date}</td>
              <td>${a.time_in ?? ''}</td>
              <td>${a.in_photo_path 
                ? `<img src="/storage/${a.in_photo_path}" width="60" style="cursor:pointer" onclick="previewPhoto('/storage/${a.in_photo_path}')">`
                : 'No In Photo'}
              </td>
              <td>${a.time_out ?? ''}</td>
              <td>${a.out_photo_path 
                ? `<img src="/storage/${a.out_photo_path}" width="60" style="cursor:pointer" onclick="previewPhoto('/storage/${a.out_photo_path}')">`
                : 'No Out Photo'}
              </td>
              <td>${a.status ?? 'Present'}</td>
              <td>
                ${userPerms.includes('attendance-delete')
                  ? `<button class="btn btn-sm btn-danger" onclick="deleteAttendance(${a.id})">Delete</button>`
                  : ''}
              </td>
            </tr>`;
        });

        // Decide Punch In / Punch Out button
        checkPunchStatus(attendances);
      })
      .catch(err => console.error("Error loading attendances:", err));
  }

  window.previewPhoto = function(url) {
    Swal.fire({
      imageUrl: url,
      imageAlt: 'Attendance Photo',
      showCloseButton: true,
      showConfirmButton: false,
      width: 'auto',
      background: '#fff',
    });
  }

  window.openCreateModal = function() {
    document.getElementById('editId').value = '';
    document.getElementById('in_photo').value = '';
    document.getElementById('out_photo').value = '';
    document.getElementById('inPhotoBlock').style.display = 'block';
    document.getElementById('outPhotoBlock').style.display = 'none';
    $('#attendanceModal').modal('show');

    document.getElementById('attendanceForm').onsubmit = function(e) {
      e.preventDefault();
      punchIn();
    };
  }

  window.punchIn = async function() {
    try {
      const formData = new FormData();
      const photo = document.getElementById('in_photo').files[0];
      if (photo) formData.append('in_photo', photo);

      const res = await api.post('/attendances/punch-in', formData);
      $('#attendanceModal').modal('hide');
      await loadAttendances();
      
      // ✅ Refresh sidebar immediately
      await refreshSidebar();

      Swal.fire({ toast:true, position:'top-end', icon:'success', title:res.data.message, showConfirmButton:false, timer:3000 });
    } catch (err) {
      Swal.fire({ toast:true, position:'top-end', icon:'error', title: err.response?.data?.message || 'Error punching in', showConfirmButton:false, timer:3000 });
    }
  }

  window.punchOut = async function(id) {
    document.getElementById('inPhotoBlock').style.display = 'none';
    document.getElementById('outPhotoBlock').style.display = 'block';
    $('#attendanceModal').modal('show');

    document.getElementById('attendanceForm').onsubmit = async function(e) {
      e.preventDefault();
      try {
        const formData = new FormData();
        const photo = document.getElementById('out_photo').files[0];
        if (photo) formData.append('out_photo', photo);

        const res = await api.post(`/attendances/${id}/punch-out`, formData);
        $('#attendanceModal').modal('hide');
        await loadAttendances();
      // ✅ Refresh sidebar immediately
      await refreshSidebar();

        Swal.fire({ toast:true, position:'top-end', icon:'success', title:res.data.message, showConfirmButton:false, timer:3000 });
      } catch (err) {
        Swal.fire({ toast:true, position:'top-end', icon:'error', title: err.response?.data?.message || 'Error punching out', showConfirmButton:false, timer:3000 });
      }
    };
  }

  window.deleteAttendance = function(id) {
    Swal.fire({
      title: 'Are you sure?',
      text: 'This will delete the attendance record',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        api.delete(`/attendances/${id}`)
          .then(() => loadAttendances())
          .catch(err => console.error("Error deleting attendance:", err));
      }
    });
  }

  document.addEventListener("DOMContentLoaded", initAttendance);
  document.addEventListener('livewire:navigated', initAttendance);
})();
</script>
@endpush
