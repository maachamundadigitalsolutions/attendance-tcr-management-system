<?php

namespace App\Livewire;
use Livewire\Component;

class Attendances extends Component
{
    public function render()
    {
        return view('livewire.attendances')
          ->layout('layouts.app');
    }
}
