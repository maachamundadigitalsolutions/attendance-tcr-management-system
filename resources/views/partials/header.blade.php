<!-- Main Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a onclick="logout()" class="nav-link">Logout</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">

    <!-- Notifications Dropdown -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        <span class="badge badge-warning navbar-badge" id="notifCount">0</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notifDropdown">
        <span class="dropdown-item text-muted">Loading...</span>
      </div>
    </li>

    <!-- Fullscreen -->
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#"><i class="fas fa-expand-arrows-alt"></i></a>
    </li>
  </ul>
</nav>

<script>
function logout() {
    api.post('/logout')
      .then(() => {
        localStorage.clear();
        window.location.href = "/login";
      })
      .catch(err => {
        console.error("Logout failed:", err);
      });
}

async function loadNotifications() {
  try {
    // Sanctum ma token already set che, user_id optional che
    const res = await api.get('/notifications');
    const list = res.data.notifications || [];

    // Update count
    document.getElementById('notifCount').innerText = list.length;

    // Build dropdown HTML
    let html = '';
    if (list.length === 0) {
      html = '<span class="dropdown-item text-muted">No new notifications</span>';
    } else {
      list.forEach(n => {
        const data = n.data || {};
        html += `
          <a href="#" class="dropdown-item" onclick="markAsRead('${n.id}')">
            <i class="fas fa-clock mr-2"></i>
            <strong>${data.title || 'Notification'}</strong><br>
            ${data.message || ''}
            <span class="float-right text-muted text-sm">${dayjs(n.created_at).fromNow()}</span>
          </a>
          <div class="dropdown-divider"></div>
        `;
      });
    }
    document.getElementById('notifDropdown').innerHTML = html;
  } catch (err) {
    console.log('err2', err);
    console.error("Error loading notifications:", err);
    document.getElementById('notifDropdown').innerHTML =
      '<span class="dropdown-item text-danger">Error loading notifications</span>';
  }
}

async function markAsRead(id) {
  try {
    await api.post(`/notifications/${id}/read`);
    loadNotifications(); // refresh after marking
  } catch (err) {
    console.log('err', err);
    
    console.error("Error marking notification:", err);
  }
}

// 🔄 Run on page load
document.addEventListener("DOMContentLoaded", () => {
  loadNotifications();
});

// 🔄 Refresh periodically
setInterval(loadNotifications, 60000); // every 1 min
</script>
