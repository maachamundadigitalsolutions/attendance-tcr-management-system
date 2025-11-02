<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Login extends Component
{
    public $loginField = '';
    public $password = '';

    public function login()
    {
        $response = Http::post('http://127.0.0.1:8001/api/v1/login', [
            'login'    => $this->loginField,
            'password' => $this->password,
        ]);

        if ($response->successful()) {
            session(['api_token' => $response->json('token')]);
            return redirect()->to('/dashboard');
        }

        $this->addError('loginField', $response->json('message') ?? 'Login failed.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.app');
    }
}

