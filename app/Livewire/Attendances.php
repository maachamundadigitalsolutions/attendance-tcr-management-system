<?php

namespace App\Livewire;
use Livewire\Component;

class Attendances extends Component
{
    protected $listeners = ['refreshTable' => '$refresh'];
    
    public function render()
    {
        return view('livewire.attendances')
          ->layout('layouts.app');
    }
}
