<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('register');
    }

    public function showStudentForm()
    {
        return view('register-student');
    }

    public function showTeacherForm()
    {
        return view('register-teacher');
    }

    public function showLecturerForm()
    {
        return view('register-lecturer');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
            'school_id' => [
                'required_unless:role,super_admin',
                'exists:schools,id',
                'nullable',
            ],
            'role' => ['required', Rule::in(['siswa', 'guru', 'super_admin'])],
            'level' => ['required_if:role,siswa', 'in:1,2,3', 'nullable'],
            'class' => ['required_if:role,siswa', 'regex:/^[a-zA-Z]$/', 'nullable'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'school_id' => $request->school_id,
            'level' => $request->role === 'siswa' ? $request->level : null,
            'class' => $request->role === 'siswa' ? $request->class : null,
            'status' => in_array($request->role, ['guru', 'super_admin']) ? 'pending' : 'verified',
        ]);

        $user->assignRole($request->role);

        if ($user->status === 'pending') {
            Auth::login($user);
            return redirect()->route('teacher.waiting');
        }

        Auth::login($user);
        return redirect()->intended('/admin');
    }
}