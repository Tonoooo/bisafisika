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

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'school_id' => 'nullable',
        ];
    }

    public function register()
    {
        $validatedData = $this->validate();

        if ($validatedData['school_id'] === '') {
            $validatedData['school_id'] = null;
        }

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'school_id' => $validatedData['school_id'],
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