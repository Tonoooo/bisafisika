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

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'school_id' => 'nullable',
            'level' => 'nullable',
            'class' => 'nullable',
        ];
    }

    public function register()
    {
        $validatedData = $this->validate();

        if ($validatedData['school_id'] === '') {
            $validatedData['school_id'] = null;
        }
        if ($validatedData['level'] === '') {
            $validatedData['level'] = null;
        }
        if ($validatedData['class'] === '') {
            $validatedData['class'] = null;
        }

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'school_id' => $validatedData['school_id'],
            'level' => $validatedData['level'],
            'class' => $validatedData['class'],
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