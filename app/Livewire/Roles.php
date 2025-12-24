<?php

namespace App\Livewire;

use Livewire\Component;

class Roles extends Component
{ 
     protected $listeners = ['refreshTable' => '$refresh'];
     public function render()
    {
        // We don’t fetch users here because we’re using Axios on the frontend
        return view('livewire.roles')
          ->layout('layouts.app');
    }
}
