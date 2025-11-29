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
              <div class="form-group">
                <label>Status</label>
                <select id="status" class="form-control" name="status">
                  <option value="present">Present</option>
                  <option value="absent">Absent</option>
                  <option value="leave">Leave</option>
                </select>
              </div>
           <div class="form-group" id="inPhotoBlock">
              <label>In Photo</label>
              <input type="file" id="in_photo" name="in_photo" class="form-control" accept="image/*" capture="camera">
            </div>

            <div class="form-group" id="outPhotoBlock" style="display:none;">
              <label>Out Photo</label>
              <input type="file" id="out_photo" name="out_photo" class="form-control" accept="image/*" capture="camera">
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
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- AdminLTE DataTables assets -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

<script>
(function () {
  let attendanceInitialized = false;

  function initAttendance() {
    if (attendanceInitialized) return;
    attendanceInitialized = true;

    if (!window.location.pathname.includes('attendances')) return;

    axios.get('/me')
      .then(res => {
        const perms = res.data.permissions || [];
        if (perms.includes('attendance-mark')) {
          document.getElementById('attendanceFormBlock').style.display = 'block';
        }
        if (perms.includes('attendance-view-all') || perms.includes('attendance-mark')) {
          document.getElementById('attendanceListBlock').style.display = 'block';
          loadAttendances(perms);
        }
      })
      .catch(err => console.error("Error fetching user data:", err));
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

  function loadAttendances(userPerms = []) {
  axios.get('/attendances')
    .then(res => {
      const tbody = document.getElementById('attendance-table');
      if (!tbody) return;

      tbody.innerHTML = '';
      res.data.data.forEach(a => {
        tbody.innerHTML += `
          <tr>
            <td>${a.id}</td>
            <td>${a.user?.name ?? a.user_id}</td>
            <td>${a.date}</td>
            <td>${a.time_in ?? ''}</td>
            <td>
              ${a.in_photo_path 
                ? `<img src="/storage/${a.in_photo_path}" width="60" style="cursor:pointer" onclick="previewPhoto('/storage/${a.in_photo_path}')">`
                : 'No In Photo'}
            </td>
            <td>${a.time_out ?? ''}</td>
            <td>
              ${a.out_photo_path 
                ? `<img src="/storage/${a.out_photo_path}" width="60" style="cursor:pointer" onclick="previewPhoto('/storage/${a.out_photo_path}')">`
                : 'No Out Photo'}
            </td>
            <td>${a.status ?? 'Present'}</td>
            <td>
              ${!a.time_out ? `<button class="btn btn-sm btn-warning" onclick="punchOut(${a.id})">Punch Out</button>` : ''}
              ${userPerms.includes('attendance-delete')
                ? `<button class="btn btn-sm btn-danger" onclick="deleteAttendance(${a.id})">Delete</button>`
                : ''}
            </td>
          </tr>`;
      });

      if ($.fn.DataTable.isDataTable('#attendanceTable')) {
        $('#attendanceTable').DataTable().destroy();
      }
      $('#attendanceTable').DataTable({
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        pageLength: 10,
        dom: 'Bfrtip',
        buttons: [
          { extend: 'excelHtml5', className: 'btn btn-success btn-sm', text: '📊 Excel', exportOptions: { columns: ':not(:last-child)' } },
          { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm', text: '📄 PDF', exportOptions: { columns: ':not(:last-child)' } },
          { extend: 'print', className: 'btn btn-info btn-sm', text: '🖨️ Print', exportOptions: { columns: ':not(:last-child)' } },
          { extend: 'colvis', className: 'btn btn-secondary btn-sm', text: '👁️ Columns' }
        ]
      }).buttons().container().appendTo('#attendanceTable_wrapper .col-md-6:eq(0)');
    })
    .catch(err => console.error("Error loading attendances:", err));
}




  window.openCreateModal = function() {
  // Punch In mode
  document.getElementById('editId').value = '';
  // document.getElementById('status').value = 'present';
  document.getElementById('in_photo').value = '';
  document.getElementById('out_photo').value = '';

  // Show only In Photo
  document.getElementById('inPhotoBlock').style.display = 'block';
  document.getElementById('outPhotoBlock').style.display = 'none';

  $('#attendanceModal').modal('show');

    // Bind Punch In
    document.getElementById('attendanceForm').onsubmit = function(e) {
      e.preventDefault();
      punchIn();
    };
  }

  window.punchIn = async function() {
  // Show modal only for In Photo
  document.getElementById('inPhotoBlock').style.display = 'block';
  document.getElementById('outPhotoBlock').style.display = 'none';
  $('#attendanceModal').modal('show');

  document.getElementById('attendanceForm').onsubmit = async function(e) {
    e.preventDefault();
    try {
      const formData = new FormData();
      const photo = document.getElementById('in_photo').files[0];
      if (photo) formData.append('in_photo', photo);

      // Add other fields if needed
      // const status = document.getElementById('status').value;
      // const remarks = document.getElementById('remarks').value;
      formData.append('status', 'Present');
      formData.append('remarks', remarks);

      const res = await axios.post('/attendances/punch-in', formData);
      $('#attendanceModal').modal('hide');
        await loadAttendances();   // ensure refresh
        Swal.fire({ toast:true, position:'top-end', icon:'success', title:res.data.message, showConfirmButton:false, timer:3000 });
      } catch (err) {
        Swal.fire({ toast:true, position:'top-end', icon:'error', title: err.response?.data?.message || 'Error punching in', showConfirmButton:false, timer:3000 });
      }
    };
  }


  window.punchOut = async function(id) {
    // Show modal only for Out Photo
    document.getElementById('inPhotoBlock').style.display = 'none';
    document.getElementById('outPhotoBlock').style.display = 'block';
    $('#attendanceModal').modal('show');

    document.getElementById('attendanceForm').onsubmit = async function(e) {
      e.preventDefault();
      try {
        const formData = new FormData();
        const photo = document.getElementById('out_photo').files[0];
        if (photo) formData.append('out_photo', photo);

        const res = await axios.post(`/attendances/${id}/punch-out`, formData);
        $('#attendanceModal').modal('hide');
        await loadAttendances();   // ensure refresh
        Swal.fire({ toast:true, position:'top-end', icon:'success', title:res.data.message, showConfirmButton:false, timer:3000 });
      } catch (err) {
        Swal.fire({ toast:true, position:'top-end', icon:'error', title: err.response?.data?.message || 'Error punching out', showConfirmButton:false, timer:3000 });
      }
    };
  }


  window.deleteAttendance = function(id) {
    if (confirm("Delete this attendance?")) {
      axios.delete(`/attendances/${id}`)
        .then(() => loadAttendances())
        .catch(err => console.error("Error deleting attendance:", err));
    }
  }

  window.openCreateModal = function() {
    document.getElementById('editId').value = '';
    document.getElementById('status').value = 'present';
    // document.getElementById('remarks').value = '';
    document.getElementById('photo').value = '';
    $('#attendanceModal').modal('show');

    // Bind Punch In
    document.getElementById('attendanceForm').onsubmit = function(e) {
      e.preventDefault();
      punchIn();
    };
  }

  document.addEventListener("DOMContentLoaded", initAttendance);
  document.addEventListener("livewire:navigated", initAttendance);
})();
</script>
@endpush
