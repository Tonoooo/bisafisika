<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterStudent extends Component
{
    public $name;
    public $email;
    public $password;
    public $school_id;
    public $level;
    public $class;
    public $role = 'siswa';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
        'school_id' => 'required|exists:schools,id',
        'level' => 'required|in:1,2,3',
        'class' => 'required|regex:/^[a-zA-Z]$/',
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'school_id' => $this->school_id,
            'level' => $this->level,
            'class' => $this->class,
            'status' => 'verified',
        ]);

        $user->assignRole('siswa');
        Auth::login($user);

        return redirect()->intended('/admin');
    }

    public function render()
    {
        return view('livewire.register-student', [
            'schools' => School::all()
        ]);
    }
} 