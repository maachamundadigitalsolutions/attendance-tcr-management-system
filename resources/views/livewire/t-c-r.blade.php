<div>

  <!-- Admin Bulk Assign -->
  <div class="card mt-4" id="bulkAssignCard" style="display:none;">
    <div class="card-header">
      <h3 class="card-title">Bulk Assign TCRs (Admin)</h3>
    </div>
    <div class="card-body">
      <form id="bulkAssignForm">
        <div class="form-group">
          <label>First TCR No</label>
          <input type="number" name="first_tcr_no" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Last TCR No</label>
          <input type="number" name="last_tcr_no" class="form-control" required>
        </div>
       <div class="form-group">
        <label>Assign To Employee</label>
        <select name="user_id" id="employeeSelect" class="form-control" required>
          <option value="" disabled selected>-- Select Employee --</option>
        </select>

      </div>

        <button type="submit" class="btn btn-primary">Assign Range</button>
      </form>
    </div>
  </div>


  <!-- Card -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">TCR Records</h3>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="tcrTable" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>TCR No</th>
              <th>SR No</th>
              <th>Employee ID</th>
              <th>Status</th>
              <th>Payment Term</th>
              <th>Amount</th> <!-- 👈 New column -->
              <th>TCR Photo</th>
              <th>Payment Screenshot</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>


  <!-- Modal (Employee Use TCR) -->
  <div class="modal fade" id="tcrModal" tabindex="-1" role="dialog" aria-labelledby="tcrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <form id="tcrForm" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="tcrModalLabel">Use TCR</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" id="tcr_id">

            <div class="form-group">
              <label>Select Assigned TCR</label>
              <select name="tcr_id" id="tcr_id_select" class="form-control"></select>
            </div>

            <div class="form-group">
              <label>Service Order No (SR No)</label>
              <input type="text" name="sr_no" id="sr_no" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Payment Term</label>
              <select name="payment_term" id="payment_term" class="form-control" required>
                <option value="case">Case</option>
                <option value="online">Online</option>
              </select>
            </div>

            <div class="form-group">
              <label>Amount</label> <!-- 👈 New field -->
              <input type="number" name="amount" id="amount" class="form-control" required>
            </div>

            <div class="form-group">
              <label>TCR Photo</label>
              <input type="file" name="tcr_photo" id="tcr_photo" class="form-control" required>
            </div>

            <div class="form-group" id="screenshotDiv" style="display:none;">
              <label>Payment Screenshot (if online)</label>
              <input type="file" name="payment_screenshot" id="payment_screenshot" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Submit</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function () {

  if (window.__tcrInitAttached) return;
  window.__tcrInitAttached = true;

  // Axios setup
  axios.defaults.baseURL = 'http://192.168.1.27:8001/api/v1';
  axios.defaults.headers.common['Accept'] = 'application/json';
  axios.interceptors.request.use(config => {
    const token = localStorage.getItem('api_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  });

  function bindTcrEvents(tableApi) {
    $('#tcrTable').off('click', '.useBtn').on('click', '.useBtn', function () {
      const id = $(this).data('id');
      document.getElementById('tcr_id').value = id;
      $('#tcrModal').modal('show');
    });

    $('#tcrTable').off('click', '.verifyCaseBtn').on('click', '.verifyCaseBtn', function () {
      const id = $(this).data('id');
      axios.post(`/tcrs/${id}/verify`, { action: 'verified' })
        .then(() => location.reload())
        .catch(() => alert("Verification failed"));
    });

    $('#tcrTable').off('click', '.verifyOnlineBtn').on('click', '.verifyOnlineBtn', function () {
      const id = $(this).data('id');
      axios.post(`/tcrs/${id}/verify`, { action: 'verified' })
        .then(() => location.reload())
        .catch(() => alert("Verification failed"));
    });

    $('#tcrTable').off('click', '.deleteBtn').on('click', '.deleteBtn', function () {
      const id = $(this).data('id');
      axios.delete(`/tcrs/${id}`)
        .then(() => {
          tableApi.row($(this).parents('tr')).remove().draw();
        })
        .catch(() => alert("Delete failed"));
    });
  }

  let dt; // keep one DataTable instance

  function initTcrTable() {
    if (!window.location.pathname.includes('tcr')) return;

    if (!$.fn.DataTable.isDataTable('#tcrTable')) {
      dt = $("#tcrTable").DataTable({
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 10,
        buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
        drawCallback: function () {
          bindTcrEvents(this.api());
        }
      });
      dt.buttons().container().appendTo('#tcrTable_wrapper .col-md-6:eq(0)');
    } else {
      dt.clear();
    }

    window.previewPhoto = function(url) {
      Swal.fire({
        imageUrl: url,
        imageAlt: 'Preview',
        showCloseButton: true,
        showConfirmButton: false,
        width: 'auto',
        background: '#fff',
      });
    }

    // Load all TCRs
    axios.get('/tcrs')
      .then(res => {
        dt.clear();
        res.data.forEach(r => {
          console.log('R',r);
          
          dt.row.add([
            r.tcr_no ?? '—',
            r.sr_no ?? '—',
            r.user_id ?? '—',
            r.status ?? '—',
            r.payment_term ?? '—',
            r.amount ?? '—',
            r.tcr_photo ? `<img src="/storage/${r.tcr_photo}" width="60" style="cursor:pointer" onclick="previewPhoto('/storage/${r.tcr_photo}')">` : 'No Photo',
            r.payment_screenshot ? `<img src="/storage/${r.payment_screenshot}" width="60" style="cursor:pointer" onclick="previewPhoto('/storage/${r.payment_screenshot}')">` : 'No Screenshot',
            `
            ${r.status === 'assigned' && userPerms.includes('tcr-use')
              ? `<button class="btn btn-success btn-sm useBtn" data-id="${r.id}">Use</button>` : ''}

            ${r.status === 'used' && r.payment_term === 'case' && userPerms.includes('tcr-verify-case')
              ? `<button class="btn btn-info btn-sm verifyCaseBtn" data-id="${r.id}">Verify Case</button>` : ''}

            ${r.status === 'used' && r.payment_term === 'online' && userPerms.includes('tcr-verify-online')
              ? `<button class="btn btn-warning btn-sm verifyOnlineBtn" data-id="${r.id}">Verify Online</button>` : ''}

            ${userPerms.includes('tcr-delete')
              ? `<button class="btn btn-danger btn-sm deleteBtn" data-id="${r.id}">Delete</button>` : ''}
          `
          ]);
        });
        dt.draw(false);
        bindTcrEvents(dt); // ✅ ensure events after load
      })
      .catch(err => console.error("Error loading TCRs:", err));

    // Populate dropdowns
    axios.get('/tcrs/assigned').then(res => {
      const select = document.getElementById('tcr_id_select');
      if (select) {
        select.innerHTML = '';
        res.data.forEach(tcr => {
          select.insertAdjacentHTML('beforeend', `<option value="${tcr.id}">TCR No ${tcr.tcr_no}</option>`);
        });
      }
    });

    axios.get('/users/engineers').then(res => {
      const select = document.getElementById('employeeSelect');
      if (select) {
        select.innerHTML = '<option value="" disabled selected>-- Select Employee --</option>';
        const seen = new Set();
        res.data.forEach(emp => {
          if (!seen.has(emp.id)) {
            seen.add(emp.id);
            select.insertAdjacentHTML('beforeend',
              `<option value="${emp.id}">${emp.name} (${emp.user_id})</option>`);
          }
        });
      }
    });
  }

  // Hooks
  document.addEventListener('DOMContentLoaded', initTcrTable);
  document.addEventListener('livewire:navigated', initTcrTable);
  Livewire.hook('message.processed', () => {
    if (window.location.pathname.includes('tcr')) {
      initTcrTable();
    }
  });

})();
</script>
@endpush
