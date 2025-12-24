<?php
namespace App\Livewire;

use Livewire\Component;

class Users extends Component
{
    protected $listeners = ['refreshTable' => '$refresh'];
    public function render()
    {
        return view('livewire.users')
            ->layout('layouts.app');
    }
}
