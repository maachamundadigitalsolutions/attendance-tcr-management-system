<?php

namespace App\Livewire;

use Livewire\Component;

class TCR extends Component
{
    public function render()
    {
        return view('livewire.t-c-r')
          ->layout('layouts.app');
    }
}
