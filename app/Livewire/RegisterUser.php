<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterUser extends Component
{
    public $role;
    public $name;
    public $email;
    public $password;
    public $school_id;
    public $level;
    public $class;

    protected $layout = 'layouts.guest';

    protected $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'confirmed', 'min:8'],
        'school_id' => ['required', 'exists:schools,id'],
        'level' => ['required_if:role,siswa', 'in:1,2,3'],
        'class' => ['required_if:role,siswa', 'regex:/^[a-zA-Z]$/'],
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'school_id' => $this->school_id,
            'level' => $this->role === 'siswa' ? $this->level : null,
            'class' => $this->role === 'siswa' ? $this->class : null,
            'status' => $this->role === 'guru' ? 'pending' : 'verified',
        ]);

        if ($this->role === 'siswa') {
            $user->assignRole('siswa');
        } elseif ($this->role === 'guru') {
            $user->assignRole('guru');
        }

        Auth::login($user);

        if ($this->role === 'guru' && $user->status === 'pending') {
            return redirect()->route('teacher.waiting');
        }

        return redirect()->intended('/admin');
    }

    public function render()
    {
        return view('livewire.register-user');
    }
}