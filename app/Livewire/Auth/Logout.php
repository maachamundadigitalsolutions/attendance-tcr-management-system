<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Logout extends Component
{
    public function logout()
    {
        // API call with token
        Http::withToken(session('api_token'))
            ->post(config('app.api_url') . '/v1/logout');

        // Clear session
        session()->forget(['api_token', 'user']);

        return redirect('/login');
    }

    public function render()
    {
        return view('livewire.auth.logout');
    }
}
