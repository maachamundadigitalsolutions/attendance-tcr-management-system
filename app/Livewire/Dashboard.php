<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public $tasks = 150;
    public $completion = 53;

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('layouts.app'); // 👈 AdminLTE layout use karo
    }
}
