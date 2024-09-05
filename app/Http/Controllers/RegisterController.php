<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Murid;
use App\Models\Ustaz;
use App\Models\Umum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class RegisterController extends Controller
{

    public function index()
    {
        // // Ambil data kota/kabupaten dari API
        // $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
        // $cities = $response->json()['data'];

        try {
            // Ambil data kota/kabupaten dari API
            $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
            $cities = $response->json()['data'];
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, arahkan kembali dengan pesan error
            return view('no_internet')->withErrors(['error' => 'Tidak dapat mengakses data kota/kabupaten. Pastikan Anda terhubung ke internet.']);
        }

        // dd($cities);

        return view('auth.register', [
            'cities' => $cities,
        ]);
    }

    public function register(Request $request)
    {
        // dd($request->all());
        // Validasi data yang diterima dari form pendaftaran
        $request->validate([
            'nama' => 'required|string|max:100',
            'date' => 'required|date_format:d-m-Y',
            'alamat' => 'required|string',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'telp' => 'required|string|max:15',
            'username' => 'required|string|max:20|unique:umum,username|unique:users,username',
            'email' => 'required|string|email|max:40|unique:umum,email',
            'password' => 'required|string|min:8',
        ], [
            'nama.required' => '*Nama wajib diisi',
            'date.required' => '*Tanggal Lahir wajib diisi',
            'alamat.required' => '*Alamat wajib diisi',
            'gender.required' => '*Jenis Kelamin wajib diisi',
            'telp.required' => '*No Telepon wajib diisi',
            'username.required' => '*Username wajib diisi',
            'email.required' => '*Email wajib diisi',
            'password.required' => '*Password wajib diisi',
        ]);

        // Ambil data kota/kabupaten dari API
        $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
        $cities = $response->json()['data'];

        // Normalisasi dan cari kecocokan lokasi
        $inputLocation = strtolower($request->alamat);
        $normalizedCities = array_map(function ($city) {
            return strtolower($city['lokasi']);
        }, $cities);

        $matches = array_filter($cities, function ($city) use ($inputLocation) {
            return strtolower($city['lokasi']) === $inputLocation;
        });

        if (count($matches) === 0) {
            return redirect()->back()->withErrors(['alamat' => 'Lokasi tidak valid, pilih lokasi yang tersedia.'])->withInput();
        } elseif (count($matches) > 1) {
            return redirect()->back()->withErrors(['alamat' => 'Terdapat lebih dari satu lokasi yang cocok, silakan pilih dari daftar.'])->withInput();
        }

        // Ambil lokasi yang cocok
        $match = array_values($matches)[0]; // Mendapatkan elemen pertama
        $validLocation = $match['lokasi'];
        $formattedLocation = ucwords(strtolower($validLocation));

        // Cek apakah username sudah digunakan
        $existingUsername = User::where('username', $request->username)->first();
        if ($existingUsername) {
            return redirect()->back()->withErrors(['username' => 'Username sudah digunakan.'])->withInput();
        }

        // Cek apakah email sudah digunakan
        $existingEmail = Murid::where('email', $request->email)->exists() || Ustaz::where('email', $request->email)->exists() || Umum::where('email', $request->email)->exists();
        if ($existingEmail) {
            return redirect()->back()->withErrors(['email' => 'Email sudah digunakan.'])->withInput();
        }


        // Gunakan transaksi database untuk memastikan kedua operasi berikut sukses
        DB::transaction(function () use ($request, $formattedLocation) {
            // Simpan data ke tabel users
            User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'umum', // Set role default untuk umum
            ]);
            // Simpan data ke tabel umum
            Umum::create([
                'nama' => $request->nama,
                'tanggal_lahir' => Carbon::createFromFormat('d-m-Y', $request->date)->toDateString(),
                'alamat' => $formattedLocation,
                'gender' => $request->gender,
                'no_telepon' => $request->telp,
                'username' => $request->username,
                'email' => $request->email,
            ]);
        });

        // Redirect ke halaman login setelah berhasil mendaftar
        return redirect()->back()->with('success', 'Pendaftaran berhasil. Silahkan login.');
    }
}
