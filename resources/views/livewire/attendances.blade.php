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
            <th>Time</th>
            <th>Late</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Photo</th>
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
              <div class="form-group">
                <label>Remarks</label>
                <input type="text" id="remarks" name="remarks" class="form-control">
              </div>
              <div class="form-group">
                <label>Selfie Photo</label>
                <input type="file" id="photo" name="photo" class="form-control" accept="image/*" capture="camera">
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
            const lateBadge = a.is_late
              ? '<span class="badge badge-danger">Late</span>'
              : '<span class="badge badge-success">On Time</span>';

            tbody.innerHTML += `
              <tr class="${a.is_late ? 'table-danger' : ''}">
                <td>${a.id}</td>
                <td>${a.user?.name ?? a.user_id}</td>
                <td>${a.date}</td>
                <td>${a.time}</td>
                <td>${lateBadge}</td>
                <td>${a.status}</td>
                <td>${a.remarks ?? ''}</td>
                <td>
                  ${a.photo_path 
                    ? `<img src="/storage/${a.photo_path}" width="60" style="cursor:pointer" onclick="previewPhoto('/storage/${a.photo_path}')">` 
                    : 'No Photo'}
                </td>

                <td>
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

  document.addEventListener("DOMContentLoaded", () => {
    initAttendance();

    const form = document.getElementById('attendanceForm');
    if (form) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const id = document.getElementById('editId').value;
        const url = id ? `/attendances/${id}` : '/attendances';
        const method = id ? 'put' : 'post';

        axios({ method, url, data: formData })
          .then(() => {
            $('#attendanceModal').modal('hide');
            loadAttendances();
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: id ? 'Attendance updated successfully!' : 'Attendance marked successfully!',
              showConfirmButton: false,
              timer: 3000
            });
          })
          .catch(err => {
            console.error("Error saving attendance:", err.response || err);
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'error',
              title: err.response?.data?.message || 'Error saving attendance',
              showConfirmButton: false,
              timer: 3000
            });
          });
      });
    }
  });

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
    document.getElementById('remarks').value = '';
    document.getElementById('photo').value = '';
    $('#attendanceModal').modal('show');
  }

  document.addEventListener("livewire:navigated", initAttendance);
})();
</script>
@endpush
