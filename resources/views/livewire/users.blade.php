<div class="container mt-4">
  <h3>User Management</h3>

  <button class="btn btn-primary mb-3" wire:click="create">Add User</button>

  @if (session()->has('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <table class="table table-bordered">
    <thead>
      <tr><th>Name</th><th>Email</th><th>Actions</th></tr>
    </thead>
    <tbody>
      @foreach($users as $user)
        <tr>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>
            <button class="btn btn-sm btn-warning" wire:click="edit({{ $user->id }})">Edit</button>
            <button class="btn btn-sm btn-danger" wire:click="delete({{ $user->id }})">Delete</button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  {{ $users->links() }}

  <!-- Modal -->
<div class="modal fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog" style="@if($showModal) display:block; @endif">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form wire:submit.prevent="save">
        <div class="modal-header">
          <h5 class="modal-title">{{ $editingUserId ? 'Edit User' : 'Add User' }}</h5>
          <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
        </div>
        <div class="modal-body">
          <input type="text" wire:model="name" class="form-control mb-2" placeholder="Name">
          <input type="email" wire:model="email" class="form-control mb-2" placeholder="Email">
          <input type="password" wire:model="password" class="form-control mb-2" placeholder="Password">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
  <!-- End Modal -->
</div>
