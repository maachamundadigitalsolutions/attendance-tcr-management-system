<div>
  <h1>Attendances</h1>
  <table class="table table-bordered"  wire:ignore>
    <thead>
      <tr>
        <th>User</th>
        <th>Date</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody id="attendance-table"></tbody>
  </table>
</div>

@script
<script>
  document.addEventListener('livewire:navigated', () => {
    axios.get('/attendances') // REST API endpoint
      .then(res => {
        const tbody = document.getElementById('attendance-table');
        tbody.innerHTML = '';
        res.data.forEach(att => {
          tbody.innerHTML += `
            <tr>
              <td>${att.user.name}</td>
              <td>${att.date}</td>
              <td>${att.status}</td>
            </tr>`;
        });
      })
      .catch(err => {
        console.error(err.response?.data || err.message);
        if (err.response?.status === 401) {
            alert("Login expired, please login again.");
            localStorage.clear();
            window.location.href = "/login";
            }
      });
  });
</script>
@endscript
