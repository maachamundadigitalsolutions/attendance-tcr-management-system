<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Dashboard extends Component
{
    public $tasks = 150;
    public $completion = 53;
    public $user;

    public function mount()
    {
        $token = session('api_token');

        if (!$token) {
            return redirect()->to('/login');
        }

        $response = \Http::withToken($token)->get(config('app.api_url').'/v1/user');

        if ($response->failed()) {
            session()->forget('api_token');
            return redirect()->to('/login');
        }

        $this->user = $response->json();
    }


    public function render()
    {
        return view('livewire.dashboard')
            ->layout('layouts.app', ['user' => $this->user]); // 👈 AdminLTE layout use karo
    }
    
}
