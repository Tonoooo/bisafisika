<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterTeacher extends Component
{
    public $name;
    public $email;
    public $password;
    public $school_id;
    public $role = 'guru';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
        'school_id' => 'required|exists:schools,id',
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'school_id' => $this->school_id,
            'status' => 'pending',
        ]);

        $user->assignRole('guru');
        Auth::login($user);

        return redirect()->route('teacher.waiting');
    }

    public function render()
    {
        return view('livewire.register-teacher', [
            'schools' => School::all()
        ]);
    }
} 