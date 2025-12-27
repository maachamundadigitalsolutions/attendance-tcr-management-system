<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a class="brand-link">
        <span class="brand-text font-weight-light">TeamTracker</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu">

                {{-- Dashboard --}}
                <li class="nav-item" data-role="" data-permission="">
                    <a href="/dashboard" class="nav-link" wire:navigate>
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- User Management --}}
                <li class="nav-item d-none" data-role="admin" data-permission="">
                    <a href="/user-management" class="nav-link" wire:navigate>
                        <i class="nav-icon fas fa-users"></i>
                        <p>User Management</p>
                    </a>
                </li>

                {{-- Roles & Permissions --}}
                <li class="nav-item d-none" data-role="admin" data-permission="">
                    <a href="/roles-permissions" class="nav-link" wire:navigate>
                        <i class="nav-icon fas fa-key"></i>
                        <p>Roles & Permissions</p>
                    </a>
                </li>

                {{-- Attendances --}}
                <li class="nav-item d-none" 
                data-permission="attendance-mark,attendance-view-all">
                    <a href="/attendances-management" class="nav-link" wire:navigate>
                        <i class="nav-icon fas fa-id-badge"></i>
                        <p>Attendances</p>
                    </a>
                </li>

                {{-- TCR Management Treeview --}}
              <li class="nav-item has-treeview" data-permission="tcr-assign, tcr-view-all">
                <a class="nav-link tree-toggle" wire:navigate>
                  <i class="nav-icon fas fa-id-badge"></i>
                  <p>
                    TCR Management
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/tcr-assign" class="nav-link tree-child" wire:navigate data-permission="tcr-assign">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Assign TCR</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/tcr-verify" class="nav-link tree-child" wire:navigate data-permission="tcr-verify">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Verify TCR</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/tcr-management" class="nav-link tree-child" wire:navigate data-permission="tcr-view-all">
                      <i class="far fa-circle nav-icon"></i>
                      <p>View All TCRs</p>
                    </a>
                  </li>
                </ul>
              </li>




            </ul>
        </nav>
    </div>
</aside>
