<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Livechat;
use App\Models\Murid;
use App\Models\Ustaz;
use App\Models\Umum;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Cari user berdasarkan username
        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if ($user && $user->role === 'murid') {
            $murid = Murid::where('username', $user->username)->first();
            if ($murid && $murid->is_online) {
                return back()->withErrors([
                    'username' => 'Akun ini sudah login di perangkat lain.',
                ]);
            }
        }

        if ($user && $user->role === 'ustaz') {
            $ustaz = Ustaz::where('username', $user->username)->first();
            if ($ustaz && $ustaz->is_online) {
                return back()->withErrors([
                    'username' => 'Akun ini sudah login di perangkat lain.',
                ]);
            }
        }

        if ($user && $user->role === 'umum') {
            $umum = Umum::where('username', $user->username)->first();
            if ($umum && $umum->is_online) {
                return back()->withErrors([
                    'username' => 'Akun ini sudah login di perangkat lain.',
                ]);
            }
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $this->setOnlineStatus(true); // Set status online

            return redirect()->intended($this->redirectTo());
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    protected function redirectTo()
    {
        $role = Auth::user()->role;

        switch ($role) {
            case 'admin':
                return '/admin/ustaz';
            case 'ustaz':
                return '/ustaz/home';
            case 'murid':
                return '/murid/home';
            case 'umum':
                return '/umum/home';
            default:
                return '/login';
        }
    }

    public function logout(Request $request)
    {
        $this->setOnlineStatus(false); // Set status offline
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function setOnlineStatus($status)
    {
        $user = Auth::user();

        if ($user->role === 'murid') {
            Murid::where('username', $user->username)->update(['is_online' => $status]);
            Livechat::where('username', $user->username)->delete();
        } elseif ($user->role === 'ustaz') {
            Ustaz::where('username', $user->username)->update(['is_online' => $status]);
        } elseif ($user->role === 'umum') {
            Umum::where('username', $user->username)->update(['is_online' => $status]);
            Livechat::where('username', $user->username)->delete();
        }
    }
}
