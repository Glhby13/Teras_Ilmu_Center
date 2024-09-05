<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Murid;
use App\Models\Ustaz;
use App\Models\Umum;

class NavbarController extends Controller
{
    public function index()
    {
        // Ambil pengguna yang sedang login
        $user = Auth::user();

        // Inisialisasi variabel $data dan $cityId
        $data = null;

        // Periksa peran pengguna dan ambil data yang sesuai
        if ($user->role === 'murid') {
            $data = $user->murid; // Ambil data murid
        } elseif ($user->role === 'ustaz') {
            $data = $user->ustaz; // Ambil data ustaz
        } elseif ($user->role === 'umum') {
            $data = $user->umum; // Ambil data umum
        }
        //dd($data);

        // Kirim data murid ke view
        return view('partials.navbar', [
            'user' => $user,
            'data' => $data
        ]);
    }
}
