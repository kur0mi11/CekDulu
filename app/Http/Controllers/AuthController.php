<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()
            ->withErrors(['email' => 'Email atau password yang Anda masukkan salah.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function dashboard()
    {
        $user = auth()->user();

        if ($user->role?->name === 'User') {
            $reports = Report::with('category')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
            return view('dashboard', compact('reports'));
        }

        return view('dashboard');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $roleId = Role::where('name', 'User')->value('id');

        if (!$roleId) {
            abort(500, 'Role User belum tersedia. Jalankan: php artisan db:seed --class=RoleSeeder');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // di-hash otomatis oleh cast 'hashed' di model User
            'role_id' => $roleId,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Selamat datang di CekDulu.');
    }
}