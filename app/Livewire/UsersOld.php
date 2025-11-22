<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class UsersOld extends Component
{
    use WithPagination;

    public $search = '';
    public $name, $user_id, $password;
    public $editingUserId = null;
    
     // 👇 Add this line
    public $showModal = false;

        protected $rules = [
            'user_id'  => 'required|digits_between:5,15|unique:users,user_id',
            'name'     => 'required|string|min:3',
            'password' => 'required|min:6',
        ];


    public $user;

        public function mount()
        {
            $token = session('api_token');

            $response = \Http::withToken($token)->get(config('app.api_url').'/v1/user');

            if ($response->failed()) {
                return redirect()->to('/login');
            }

            $this->user = $response->json();
        }


    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $id;
        $this->name = $user->name;
        $this->user_id = $user->user_id;
        $this->password = '';
        $this->showModal = true;
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $user->update([
                'name'  => $this->name,
                'user_id' => $this->user_id,
                // password only if filled
                'password' => $this->password ? bcrypt($this->password) : $user->password,
            ]);
        } else {
            User::create([
                'name'     => $this->name,
                'user_id'    => $this->user_id,
                'password' => bcrypt($this->password),
            ]);
        }

        $this->resetForm();
        $this->showModal = false;
        session()->flash('success', 'User saved successfully!');
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
    }

    private function resetForm()
    {
        $this->reset(['name', 'user_id', 'password', 'editingUserId']);
    }

  public function render()
    {
        $users = User::paginate(10);

        return view('livewire.users', compact('users'))
            ->layout('layouts.app', [
                'user' => $this->user   // 👈 Sidebar ne user data pass karo
            ]);
    }

}







