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
            <th>Amount</th>
            <th>TCR Photo</th>
            <th>Payment Screenshot</th>
            <th>Verified By</th>        <!-- 👈 New column -->
            <th>Verified At</th>        <!-- 👈 New column -->
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

  <!-- Photo Preview Modal -->
  <div class="modal fade" id="photoPreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-body text-center">
          <img id="previewImage" src="" class="img-fluid" alt="Preview">
        </div>
      </div>
    </div>
  </div>

</div>




@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  
(function () {
  if (window.__tcrInitAttached) return;
  window.__tcrInitAttached = true;

  axios.interceptors.request.use(config => {
    const token = localStorage.getItem('api_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  });

  let userPerms = [];
  let dt;

 

  
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

    // Make previewPhoto available to inline onclick
    window.previewPhoto = function(url) {
      Swal.fire({
        imageUrl: url,              // 👈 preview image source
        imageAlt: 'Attendance Photo', // 👈 alt text
        showCloseButton: true,      // 👈 close (X) button
        showConfirmButton: false,   // 👈 no "OK" button
        // width: 'auto',              // 👈 auto width
        width: 1000, 
        background: '#fff',         // 👈 white background
      });
    }



    // show/hide screenshotDiv based on payment_term
  $(document).on('change', '#payment_term', function() {
    if (this.value === 'online') {
      $('#screenshotDiv').show();
      $('#payment_screenshot').attr('required', true); // optional: make required
    } else {
      $('#screenshotDiv').hide();
      $('#payment_screenshot').removeAttr('required');
    }
  });

  }

 // delegated listener → works even if Livewire re-renders
  $(document).on('submit', '#tcrForm', function(e) {
    e.preventDefault();

    const id = document.getElementById('tcr_id').value;
    const formData = new FormData(this);

    // ✅ Extra validation: if payment_term = online → screenshot required
    const paymentTerm = document.getElementById('payment_term').value;
    const screenshotInput = document.getElementById('payment_screenshot');

    if (paymentTerm === 'online' && !screenshotInput.files.length) {
      alert("Payment Screenshot is required for online payments");
      return; // stop submit
    }

    axios.post(`/tcrs/${id}/use`, formData)
      .then(() => {
        $('#tcrModal').modal('hide');
        location.reload(); // reload table after success
      })
      .catch(err => {
        console.error("TCR use failed:", err);
        alert(err.response?.data?.message || "Failed to use TCR");
      });
  });


  async function initTcrTable() {
    if (!window.location.pathname.includes('tcr')) return;

    try {
      const meRes = await axios.get('/me');
      userPerms = meRes.data.permissions || [];

      if (userPerms.includes('tcr-assign')) {
        const card = document.getElementById('bulkAssignCard');
        if (card) card.style.display = 'block';
      }
    } catch (err) {
      console.error("Permission load failed:", err);
      localStorage.clear();
      window.location.href = "/login";
      return;
    }

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
        dom: 'Bfrtip',
        buttons: [
          { extend: 'copy', className: 'btn btn-secondary btn-sm' },
          { extend: 'csv', className: 'btn btn-info btn-sm' },
          { extend: 'excel', className: 'btn btn-success btn-sm', exportOptions: { columns: ':not(:last-child)' } },
          { extend: 'pdf', className: 'btn btn-danger btn-sm', exportOptions: { columns: ':not(:last-child)' } },
          { extend: 'print', className: 'btn btn-primary btn-sm', exportOptions: { columns: ':not(:last-child)' } },
          { extend: 'colvis', className: 'btn btn-warning btn-sm' }
        ],
        drawCallback: function () {
          bindTcrEvents(this.api());
        }
      });
      dt.buttons().container().appendTo('#tcrTable_wrapper .col-md-6:eq(0)');
    } else {
      dt.clear();
    }

    // Load all TCRs
    axios.get('/tcrs')
      .then(res => {
        dt.clear();
        res.data.forEach(r => {
          dt.row.add([
            r.tcr_no ?? '—',
            r.sr_no ?? '—',
            r.user_id ?? '—',
            r.status ?? '—',
            r.payment_term ?? '—',
            r.amount ?? '—',
            r.tcr_photo ? `<img src="/storage/${r.tcr_photo}" width="60" onclick="previewPhoto('/storage/${r.tcr_photo}')">` : 'No Photo',
            r.payment_screenshot ? `<img src="/storage/${r.payment_screenshot}" width="60" onclick="previewPhoto('/storage/${r.payment_screenshot}')">` : 'No Screenshot',
            r.verified_by ?? '—',
            r.verified_at ?? '—',
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
        bindTcrEvents(dt);
      })
      .catch(err => console.error("Error loading TCRs:", err));

    // Populate dropdowns only if allowed
    // if (userPerms.includes('tcr-assign')) {
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
    // }
  }

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
