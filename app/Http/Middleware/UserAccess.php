<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class UserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Simpan URL sebelumnya di session
        if (!Session::has('previous_url')) {
            Session::put('previous_url', $request->url());
        }

        // Periksa apakah peran pengguna sesuai dengan role yang dibutuhkan
        if (Auth::check() && Auth::user()->role === $role) {
            return $next($request);
        }

        // Redirect kembali ke halaman terakhir yang dibuka jika tidak memiliki akses
        return redirect(Session::get('previous_url', '/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}
