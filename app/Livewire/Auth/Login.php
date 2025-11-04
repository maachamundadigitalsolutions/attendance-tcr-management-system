<?php

namespace App\Livewire\Auth;   // 👈 This must be present and match the folder path

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Login extends Component
{
    public $loginField = '';
    public $password = '';

   

    public function login()
    {
        $response = Http::post(config('app.api_url') . '/v1/login', [
            'loginField'    => $this->loginField,
            'password' => $this->password,
        ]);


     // 👇 Debug line
    //    dd($response->status(), $response->json());



     if ($response->successful()) {
        session(['api_token' => $response->json('token')]);
        session(['user' => $response->json('user')]);
        return redirect()->to('/dashboard');
    }

        $this->addError('loginField', $response->json('message') ?? 'Login failed.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}

