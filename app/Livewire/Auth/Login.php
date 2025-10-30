<?php
namespace App\Livewire\Auth;
namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Login extends Component
{
    public $email, $password;

    public function login()
    {
        $response = Http::post(url('/api/v1/login'), [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        if ($response->successful()) {
            $token = $response->json('token');
            session(['api_token' => $token]); // store token in session
            return redirect()->to('/dashboard');
        }

        $this->addError('email', 'Invalid credentials.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.app');
    }
}

