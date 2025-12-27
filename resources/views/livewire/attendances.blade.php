<div>
    <h1>Attendance Management</h1>

    {{-- Employee Form --}}
    <div id="attendanceEmployeeFormBlock" style="display:none;">
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
    <div class="modal fade" id="attendanceEmployeeModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form id="attendanceEmployeeForm">
            <div class="modal-header">
              <h4 class="modal-title">Attendance</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <input type="hidden" id="editId">

              <!-- In Photo -->
              <div class="form-group" id="inPhotoEmployeeBlock">
                <label>In Photo</label>
                <input type="file" id="in_photo_employee" name="in_photo_employee" class="form-control" accept="image/*" capture="camera">
              </div>

              <!-- Out Photo -->
              <div class="form-group" id="outPhotoEmployeeBlock" style="display:none;">
                <label>Out Photo</label>
                <input type="file" id="out_photo_employee" name="out_photo_employee" class="form-control" accept="image/*" capture="camera">
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

   <!-- Edit Modal -->
    {{-- Admin / EDIT MODEL --}}
  <div class="modal fade" id="attendanceEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        
        <form id="attendanceEditForm"> 
          <div class="modal-header">
            <h5 class="modal-title">Attendance</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <div class="modal-body">
            <input type="hidden" id="recordId">

            <!-- Date -->
            <div class="form-group">
              <label>Date</label>
              <input type="date" id="date" name="date" class="form-control">
            </div>

            <!-- In Time -->
            <div class="form-group">
              <label>In Time</label>
              <input type="time" id="time_in" name="time_in" class="form-control">
            </div>

           <!-- In Photo -->
            <div class="form-group">
              <label>In Photo</label>
              <!-- Old photo preview -->
              <div>
                <img id="preview_in_photo" src="" width="100" style="margin-bottom:10px;">
              </div>
              <input type="file" id="in_photo" name="in_photo" class="form-control" accept="image/*" capture="camera">
            </div>

            <!-- Out Time -->
            <div class="form-group">
              <label>Out Time</label>
              <input type="time" id="time_out" name="time_out" class="form-control">
            </div>

           <!-- Out Photo -->
          <div class="form-group">
            <label>Out Photo</label>
            <!-- Old photo preview -->
            <div>
              <img id="preview_out_photo" src="" width="100" style="margin-bottom:10px;">
            </div>
            <input type="file" id="out_photo" name="out_photo" class="form-control" accept="image/*" capture="camera">
          </div>

            <!-- Status -->
            <div class="form-group">
              <label>Status</label>
              <select id="status" name="status" class="form-control">
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Leave">Leave</option>
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
<!-- DataTables & Plugins -->
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

    const block = document.getElementById('attendanceEmployeeFormBlock');
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


 async function loadAttendances(userPerms = []) {
  try {
    const res = await api.get('/attendances');
    const attendances = res.data.data;

    // Initialize or get existing DataTable
    const table = $.fn.DataTable.isDataTable('#attendanceTable')
      ? $('#attendanceTable').DataTable()
      : $('#attendanceTable').DataTable({
          responsive: true,
          autoWidth: false,
          pageLength: 10,
          lengthChange: true,
          ordering: true,
          dom: 'Bfrtip', // this enables buttons
          buttons: [
            { extend: 'excelHtml5', text: '📊 Excel', className: 'btn btn-success btn-sm' },
            { extend: 'pdfHtml5', text: '📄 PDF', className: 'btn btn-danger btn-sm', orientation: 'landscape' },
            { extend: 'print', text: '🖨 Print', className: 'btn btn-info btn-sm' }
          ],
          columnDefs: [
            { targets: [4, 6, 8], orderable: false } // Disable ordering for images & actions
          ]
        });

    // Clear old data and add new
    table.clear();
    table.rows.add(attendances.map(a => [
      a.id,
      a.user?.name ?? a.user_id,
      a.date,
      a.time_in ?? '',
      a.in_photo_path 
        ? `<img src="/storage/${a.in_photo_path}" width="60" onclick="previewPhoto('/storage/${a.in_photo_path}')">`
        : '-',
      a.time_out ?? '',
      a.out_photo_path 
        ? `<img src="/storage/${a.out_photo_path}" width="60" onclick="previewPhoto('/storage/${a.out_photo_path}')">`
        : '-',
      a.status ?? 'Present',
      (userPerms.includes('attendance-update') || userPerms.includes('attendance-manage') || userPerms.includes('attendance-delete'))
        ? `<button class="btn btn-sm btn-primary mr-1" onclick="updateAttendance(${a.id})">Update</button>
           <button class="btn btn-sm btn-danger" onclick="deleteAttendance(${a.id})">Delete</button>`
        : ''
    ]));
    table.draw();

    checkPunchStatus(attendances);
  } catch (err) {
    console.error("Error loading attendances:", err);
  }
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
    document.getElementById('in_photo_employee').value = '';
    document.getElementById('out_photo_employee').value = '';
    document.getElementById('inPhotoEmployeeBlock').style.display = 'block';
    document.getElementById('outPhotoEmployeeBlock').style.display = 'none';
    $('#attendanceEmployeeModal').modal('show');

    document.getElementById('attendanceEmployeeForm').onsubmit = function(e) {
      e.preventDefault();
      punchIn();
    };
  }

  window.punchIn = async function() {
    try {
      const formData = new FormData();
      const photo = document.getElementById('in_photo_employee').files[0];
      if (photo) formData.append('in_photo', photo);

      const res = await api.post('/attendances/punch-in', formData);
      $('#attendanceEmployeeModal').modal('hide');
      await loadAttendances(window.userPerms);
      
      // ✅ Refresh sidebar immediately
      await refreshSidebar();

      Swal.fire({ toast:true, position:'top-end', icon:'success', title:res.data.message, showConfirmButton:false, timer:3000 });
    } catch (err) {
      Swal.fire({ toast:true, position:'top-end', icon:'error', title: err.response?.data?.message || 'Error punching in', showConfirmButton:false, timer:3000 });
    }
  }

  window.punchOut = async function(id) {
    document.getElementById('inPhotoEmployeeBlock').style.display = 'none';
    document.getElementById('outPhotoEmployeeBlock').style.display = 'block';
    $('#attendanceEmployeeModal').modal('show');

    document.getElementById('attendanceEmployeeForm').onsubmit = async function(e) {
      e.preventDefault();
      try {
        const formData = new FormData();
        const photo = document.getElementById('out_photo_employee').files[0];
        if (photo) formData.append('out_photo', photo);

        const res = await api.post(`/attendances/${id}/punch-out`, formData);
        $('#attendanceEmployeeModal').modal('hide');
        await loadAttendances();

        // ✅ Refresh sidebar immediately
        await refreshSidebar();

        Swal.fire({ toast:true, position:'top-end', icon:'success', title:res.data.message, showConfirmButton:false, timer:3000 });
      } catch (err) {
        Swal.fire({ toast:true, position:'top-end', icon:'error', title: err.response?.data?.message || 'Error punching out', showConfirmButton:false, timer:3000 });
      }
    };
  }

  window.deleteAttendance = async function(id) {
    const result = await Swal.fire({
      title: 'Are you sure?',
      text: 'This will delete the attendance record',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    });

    if (result.isConfirmed) {
      try {
        await api.delete(`/attendances/${id}`);
        // await loadAttendances();
        // ✅ Pass userPerms 
        await loadAttendances(window.userPerms);
        await refreshSidebar();
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Attendance deleted successfully!',
          showConfirmButton: false,
          timer: 3000
        });
      } catch (err) {
        console.error("Error deleting attendance:", err);
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: 'Failed to delete attendance',
          showConfirmButton: false,
          timer: 3000
        });
      }
    }
  };


  function formatTimeForInput(timeStr) {
    if (!timeStr) return '';
    // Expect "HH:MM:SS" → return "HH:MM"
    const parts = timeStr.split(':');
    if (parts.length < 2) return '';
    return `${parts[0]}:${parts[1]}`;
  }

window.updateAttendance = async function(id) {
  try {
    const res = await api.get(`/attendances/${id}`);
    const record = res.data.data;

    // Helper: convert DD-MM-YYYY → YYYY-MM-DD
   function formatDateForInput(dateStr) {
  if (!dateStr) return '';
    const parts = dateStr.split('-'); // ["08","12","2025"]
    if (parts.length !== 3) return '';
    return `${parts[2]}-${parts[1]}-${parts[0]}`; // "2025-12-08"
  }

  

    // Fill fields
    document.getElementById('recordId').value = record.id;
    document.getElementById('date').value = formatDateForInput(record.date);
    // Time fix (HH:MM:SS → HH:MM)
    document.getElementById('time_in').value = formatTimeForInput(record.time_in);
    document.getElementById('time_out').value = formatTimeForInput(record.time_out)
    document.getElementById('status').value = record.status || 'Present';

    // Show old photo previews
    document.getElementById('preview_in_photo').src = record.in_photo_path 
      ? '/storage/' + record.in_photo_path 
      : '';
    document.getElementById('preview_out_photo').src = record.out_photo_path 
      ? '/storage/' + record.out_photo_path 
      : '';

    // Show modal
    $('#attendanceEditModal').modal('show');

    // Submit handler
    document.getElementById('attendanceEditForm').onsubmit = async function(e) {
      e.preventDefault();
      const formData = new FormData();
          formData.append('date', document.getElementById('date').value);
          formData.append('time_in', document.getElementById('time_in').value);
          formData.append('time_out', document.getElementById('time_out').value);
          formData.append('status', document.getElementById('status').value);

          const inPhoto = document.getElementById('in_photo').files[0];
          if (inPhoto) formData.append('in_photo', inPhoto);

          const outPhoto = document.getElementById('out_photo').files[0];
          if (outPhoto) formData.append('out_photo', outPhoto);
          console.log('formData', formData);
          

          // Always POST with _method=PUT
          const updateRes = await api.post(`/attendances/${id}?_method=PUT`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });


      $('#attendanceEditModal').modal('hide');

      await loadAttendances(window.userPerms);
      await refreshSidebar();

      Swal.fire({
        toast:true,
        position:'top-end',
        icon:'success',
        title:updateRes.data.message,
        showConfirmButton:false,
        timer:3000
      });
    };
  } catch (err) {
    console.error("Error fetching attendance record:", err);
  }
};



  document.addEventListener("DOMContentLoaded", initAttendance);
  document.addEventListener('livewire:navigated', initAttendance);
})();
</script>
@endpush
