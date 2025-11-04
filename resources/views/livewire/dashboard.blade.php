<div class="container mt-4">
   <div class="content-wrapper">
    <h1>Welcome, {{ $user['user']['name'] }}</h1>
    <p><strong>Roles:</strong> {{ implode(', ', $user['roles']) }}</p>
    <p><strong>Permissions:</strong> {{ implode(', ', $user['permissions']) }}</p>
</div>
</div>

