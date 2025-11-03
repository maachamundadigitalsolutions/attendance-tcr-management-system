<div class="row">
    <div class="col-lg-3 col-6">
   <div class="content-wrapper">
  <section class="content-header">
    <h1>Welcome, {{ $user['user']['name'] }}</h1>
  </section>

  <section class="content">
    <p><strong>Roles:</strong> {{ implode(', ', $user['roles']) }}</p>
    <p><strong>Permissions:</strong> {{ implode(', ', $user['permissions']) }}</p>
  </section>
</div>

    </div>
</div>
