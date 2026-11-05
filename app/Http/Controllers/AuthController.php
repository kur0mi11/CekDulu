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
        $role = $user->role?->name;

        $reports = collect();
        $stats = [];

        if ($role === 'User') {
            $reports = Report::with('category')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
            $stats = $reports->countBy('status')->all();
        } elseif ($role === 'Verifier') {
            $stats = [
                'pending'       => Report::where('status', 'pending')->count(),
                'need_revision' => Report::where('status', 'need_revision')->count(),
                'done'          => Report::where('verifier_id', $user->id)
                                        ->whereNotIn('status', ['pending', 'need_revision'])
                                        ->count(),
            ];
        } elseif ($role === 'Admin') {
            $stats = Report::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all();
        }

        return view('dashboard', compact('role', 'stats', 'reports'));
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