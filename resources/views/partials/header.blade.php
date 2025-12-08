<!-- Main Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a onclick="logout()"class="nav-link">Logout</a>
       <!-- <button onclick="logout()" class="btn btn-danger mt-3">Logout</button> -->
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
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
</script>
