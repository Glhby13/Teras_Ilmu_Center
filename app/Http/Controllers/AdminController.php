<?php

namespace App\Http\Controllers;

use App\Models\Iklan;
use App\Models\User;
use App\Models\Murid;
use App\Models\Report;
use App\Models\Question;
use App\Models\Ustaz;
use App\Models\Umum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function Navbar()
    {
        // Ambil pengguna yang sedang login
        $admin = Auth::user();

        dd($admin);

        // Kirim data murid ke view
        return view('admin.navbar', [
            'admin' => $admin,
        ]);
    }

    public function index()
    {
        // Ambil pengguna yang sedang login
        $admin = Auth::user();

        //dd($user);

        // Kirim data murid ke view
        return view('admin.home', [
            'admin' => $admin,
        ]);
    }


    ///////////////////////////////////////////////////////USTAZ///////////////////////////////////////////////////////
    public function dataUstaz()
    {
        $admin = Auth::user();
        // Ambil data pengguna dengan role 'ustaz' dan data terkait dari tabel 'ustaz'
        $ustaz = User::where('role', 'ustaz')
            ->whereHas('ustaz', function ($query) {
                $query->whereNull('deleted_at'); // Hanya ambil data yang tidak di-soft delete
            })
            ->with('ustaz') // Mengambil data dari tabel 'ustaz' terkait
            ->get();
        try {
            // Ambil data kota/kabupaten dari API
            $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
            $cities = $response->json()['data'];
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, arahkan kembali dengan pesan error
            return view('no_internet')->withErrors(['error' => 'Tidak dapat mengakses data kota/kabupaten. Pastikan Anda terhubung ke internet.']);
        }

        // dd($cities);
        return view('admin.ustaz', [
            'admin' => $admin,
            'cities' => $cities,
            'editCities' => $cities,
            'ustaz' => $ustaz,

        ]);
    }

    public function createUstaz(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'nama' => 'required|string|max:100',
            'date' => 'required|date_format:d-m-Y',
            'alamat' => 'required|string',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'telp' => 'required|string|max:15',
            'username' => 'required|string|max:20|unique:murid,username|unique:users,username',
            'email' => 'required|string|email|max:40|unique:murid,email',
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
            return redirect()->back()->withErrors(['alamat' => 'Lokasi tidak valid, pilih lokasi yang tersedia.']);
        } elseif (count($matches) > 1) {
            return redirect()->back()->withErrors(['alamat' => 'Terdapat lebih dari satu lokasi yang cocok, silakan pilih dari daftar.']);
        }

        // Ambil lokasi yang cocok
        $match = array_values($matches)[0]; // Mendapatkan elemen pertama
        $validLocation = $match['lokasi'];
        $formattedLocation = ucwords(strtolower($validLocation));

        // Cek apakah username sudah digunakan
        $existingUsername = User::where('username', $request->username)->first();
        if ($existingUsername) {
            return redirect()->back()->withErrors(['username' => 'Username sudah digunakan.']);
        }

        // Cek apakah email sudah digunakan
        $existingEmail = Murid::where('email', $request->email)->exists() || Ustaz::where('email', $request->email)->exists() || Umum::where('email', $request->email)->exists();
        if ($existingEmail) {
            return redirect()->back()->withErrors(['email' => 'Email sudah digunakan.']);
        }

        // Gunakan transaksi database untuk memastikan kedua operasi berikut sukses
        DB::transaction(function () use ($request, $formattedLocation) {
            // Simpan data ke tabel users dengan password statis
            User::create([
                'username' => $request->username,
                'password' => Hash::make('ustaz'), // Password statis yang di-hash
                'role' => 'ustaz', // Set role default untuk ustaz
            ]);
            // Simpan data ke tabel ustaz
            Ustaz::create([
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
        return redirect()->back()->with('success', 'Pendaftaran berhasil');
    }

    public function softDeleteUstaz(Request $request)
    {
        $usernames = $request->input('usernames', []);

        if (!is_array($usernames)) {
            $usernames = [$usernames];
        }

        $errors = [];

        foreach ($usernames as $username) {
            // Temukan Ustaz berdasarkan username
            $ustaz = Ustaz::where('username', $username)->first();

            if ($ustaz) {
                // Cek apakah pengguna sedang online
                if ($ustaz->is_online == 1) {
                    // Jika online, tambahkan pesan error
                    $errors[] = "Pengguna dengan username $username sedang login dan tidak dapat dihapus.";
                } else {
                    // Jika tidak online, lakukan soft delete
                    $ustaz->delete();
                    // Soft delete data terkait di tabel users
                    $user = User::where('username', $username)->first();
                    if ($user) {
                        $user->delete();
                    }
                }
            }
        }

        // Jika ada error, kembalikan pesan error
        if (!empty($errors)) {
            return response()->json(['error' => $errors], 422);
        }

        return response()->json(['success' => 'Data ustaz telah dihapus.']);
    }


    public function updateUstaz(Request $request, $id_ustaz)
    {

        // dd($request->all());

        $ustaz = Ustaz::with('user')->findOrFail($id_ustaz);
        $user = $ustaz->user;

        // dd($ustaz);

        // Validasi data yang diterima
        $request->validate([
            'nama' => 'nullable|string|max:100',
            'date' => 'nullable|date_format:d-m-Y',
            'alamat' => 'nullable|string',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'telp' => 'nullable|string|max:15',
            'username' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    // Cek jika username sudah ada di tabel murid, users dan ustaz
                    if (
                        User::where('username', $value)->where('username', '!=', Auth::user()->username)->exists() ||
                        Murid::where('username', $value)->where('username', '!=', Auth::user()->username)->exists() ||
                        Ustaz::where('username', $value)->where('username', '!=', Auth::user()->username)->exists() ||
                        Umum::where('username', $value)->where('username', '!=', Auth::user()->username)->exists()
                    ) {
                        $fail('Username sudah digunakan.');
                        return back()->with('error', 'Username sudah digunakan');
                    }
                },
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:40',
                function ($attribute, $value, $fail) {
                    // Cek jika email sudah ada di tabel murid, ustaz
                    if (
                        Murid::where('email', $value)->where('email', '!=', Auth::user()->email)->exists() ||
                        Ustaz::where('email', $value)->where('email', '!=', Auth::user()->email)->exists() ||
                        Umum::where('email', $value)->where('email', '!=', Auth::user()->email)->exists()
                    ) {
                        $fail('Email sudah digunakan.');
                        return back()->with('error', 'Email sudah digunakan');
                    }
                },
            ],
        ]);

        // Persiapkan array update untuk data pengguna
        $userUpdateData = [];
        $isUsernameUpdated = false;

        if ($request->filled('username')) {
            $userUpdateData['username'] = $request->input('username');
            $isUsernameUpdated = true;
        }

        // Update data pengguna
        if (!empty($userUpdateData)) {
            $user->update($userUpdateData);
        }

        // Jika username diupdate, set is_online ke 0
        if ($isUsernameUpdated) {
            $ustaz->is_online = 0;
            $ustaz->save();
        }

        // Ambil data kota/kabupaten dari API hanya jika alamat diisi
        $formattedLocation = null;
        if ($request->filled('alamat')) {
            $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
            $cities = $response->json()['data'];

            // Normalisasi dan cari kecocokan lokasi
            $inputLocation = strtolower($request->alamat);
            $matches = array_filter($cities, function ($city) use ($inputLocation) {
                return strtolower($city['lokasi']) === $inputLocation;
            });

            if (count($matches) === 0) {
                return redirect()->back()->with('error', 'Lokasi tidak valid, pilih lokasi yang tersedia.');
            } elseif (count($matches) > 1) {
                return redirect()->back()->with('error', 'Terdapat lebih dari satu lokasi yang cocok, silakan pilih dari daftar.');
            }

            // Ambil lokasi yang cocok
            $match = array_values($matches)[0]; // Mendapatkan elemen pertama
            $validLocation = $match['lokasi'];
            $formattedLocation = ucwords(strtolower($validLocation));
        }

        // Persiapkan array update untuk data murid
        $ustazUpdateData = [];
        if ($request->filled('nama')) {
            $ustazUpdateData['nama'] = $request->input('nama');
        }

        if ($request->filled('date')) {
            $ustazUpdateData['tanggal_lahir'] = Carbon::createFromFormat('d-m-Y', $request->input('date'))->format('Y-m-d');
        }

        if ($request->filled('alamat') && $formattedLocation) {
            $ustazUpdateData['alamat'] = $formattedLocation;
        }

        if ($request->filled('gender')) {
            $ustazUpdateData['gender'] = $request->input('gender');
        }

        if ($request->filled('telp')) {
            $ustazUpdateData['no_telepon'] = $request->input('telp');
        }

        if ($request->filled('email')) {
            $ustazUpdateData['email'] = $request->input('email');
        }

        // Update data
        if (!empty($ustazUpdateData)) {
            $ustaz->update($ustazUpdateData);
        }

        // Redirect ke halaman profil dengan pesan sukses
        return redirect()->back()->with('success', 'Profil berhasil diubah.');
    }

    public function TrashdataUstaz()
    {
        $admin = Auth::user();

        // Ambil data pengguna dengan role 'ustaz' dan data terkait dari tabel 'ustaz' yang telah di-soft delete
        $ustaz = User::where('role', 'ustaz')
            ->withTrashed() // Mengambil data dari tabel 'users' yang telah di-soft delete
            ->whereHas('ustaz', function ($query) {
                $query->onlyTrashed(); // Mengambil data dari tabel 'ustaz' yang telah di-soft delete
            })
            ->with(['ustaz' => function ($query) {
                $query->onlyTrashed(); // Mengambil data dari tabel 'ustaz' yang telah di-soft delete
            }])
            ->get();

        try {
            // Ambil data kota/kabupaten dari API
            $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
            $cities = $response->json()['data'];
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, arahkan kembali dengan pesan error
            return view('no_internet')->withErrors(['error' => 'Tidak dapat mengakses data kota/kabupaten. Pastikan Anda terhubung ke internet.']);
        }

        return view('admin.trash.ustaz', [
            'admin' => $admin,
            'cities' => $cities,
            'editCities' => $cities,
            'ustaz' => $ustaz,
        ]);
    }

    public function restoreUstaz(Request $request)
    {
        $usernames = $request->input('usernames', []);

        if (!is_array($usernames)) {
            $usernames = [$usernames];
        }

        $errors = [];

        foreach ($usernames as $username) {
            // Temukan Ustaz berdasarkan username
            $ustaz = Ustaz::withTrashed()->where('username', $username)->first();

            if ($ustaz) {
                // Temukan User berdasarkan username
                $user = User::withTrashed()->where('username', $username)->first();

                if ($user) {
                    // Jika User dan Ustaz ditemukan, lakukan restore
                    try {
                        $ustaz->restore();
                        $user->restore();
                    } catch (\Exception $e) {
                        // Jika terjadi kesalahan saat restore, tambahkan pesan error
                        $errors[] = "Gagal memulihkan data untuk username $username: " . $e->getMessage();
                    }
                } else {
                    $errors[] = "Pengguna dengan username $username tidak ditemukan di tabel users.";
                }
            } else {
                $errors[] = "Ustaz dengan username $username tidak ditemukan di tabel ustaz.";
            }
        }

        // Jika ada error, kembalikan pesan error
        if (!empty($errors)) {
            return response()->json(['error' => $errors], 422);
        }

        return response()->json(['success' => 'Data ustaz telah dipulihkan.']);
    }

    public function DeleteUstaz(Request $request)
    {
        $usernames = $request->input('usernames', []);

        if (!is_array($usernames)) {
            $usernames = [$usernames];
        }

        $errors = [];

        foreach ($usernames as $username) {
            // Temukan Ustaz berdasarkan username yang telah soft deleted
            $ustaz = User::onlyTrashed()->where('username', $username)->first();

            if ($ustaz) {
                // Cek apakah pengguna sedang online
                if ($ustaz->is_online == 1) {
                    // Jika online, tambahkan pesan error
                    $errors[] = "Pengguna dengan username $username sedang login dan tidak dapat dihapus.";
                } else {
                    // Jika tidak online, lakukan soft delete
                    $ustaz->forceDelete();
                }
            }
        }

        // Jika ada error, kembalikan pesan error
        if (!empty($errors)) {
            return response()->json(['error' => $errors], 422);
        }

        return response()->json(['success' => 'Data ustaz telah dihapus.']);
    }


    ///////////////////////////////////////////////////////MURID///////////////////////////////////////////////////////
    public function dataMurid()
    {
        $admin = Auth::user();
        // Ambil data pengguna dengan role 'murid' dan data terkait dari tabel 'murid'
        $murid = User::where('role', 'murid')
            ->whereHas('murid', function ($query) {
                $query->whereNull('deleted_at'); // Hanya ambil data yang tidak di-soft delete
            })
            ->with('murid') // Mengambil data dari tabel 'murid' terkait
            ->get();
        try {
            // Ambil data kota/kabupaten dari API
            $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
            $cities = $response->json()['data'];
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, arahkan kembali dengan pesan error
            return view('no_internet')->withErrors(['error' => 'Tidak dapat mengakses data kota/kabupaten. Pastikan Anda terhubung ke internet.']);
        }

        // dd($cities);
        return view('admin.murid', [
            'admin' => $admin,
            'cities' => $cities,
            'editCities' => $cities,
            'murid' => $murid,

        ]);
    }

    public function createMurid(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'nama' => 'required|string|max:100',
            'date' => 'required|date_format:d-m-Y',
            'alamat' => 'required|string',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'telp' => 'required|string|max:15',
            'username' => 'required|string|max:20|unique:murid,username|unique:users,username',
            'email' => 'required|string|email|max:40|unique:murid,email',
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
            return redirect()->back()->withErrors(['alamat' => 'Lokasi tidak valid, pilih lokasi yang tersedia.']);
        } elseif (count($matches) > 1) {
            return redirect()->back()->withErrors(['alamat' => 'Terdapat lebih dari satu lokasi yang cocok, silakan pilih dari daftar.']);
        }

        // Ambil lokasi yang cocok
        $match = array_values($matches)[0]; // Mendapatkan elemen pertama
        $validLocation = $match['lokasi'];
        $formattedLocation = ucwords(strtolower($validLocation));

        // Cek apakah username sudah digunakan
        $existingUsername = User::where('username', $request->username)->first();
        if ($existingUsername) {
            return redirect()->back()->withErrors(['username' => 'Username sudah digunakan.']);
        }

        // Cek apakah email sudah digunakan
        $existingEmail = Murid::where('email', $request->email)->exists() || Ustaz::where('email', $request->email)->exists() || Umum::where('email', $request->email)->exists();
        if ($existingEmail) {
            return redirect()->back()->withErrors(['email' => 'Email sudah digunakan.']);
        }

        // Gunakan transaksi database untuk memastikan kedua operasi berikut sukses
        DB::transaction(function () use ($request, $formattedLocation) {
            // Simpan data ke tabel users dengan password statis
            User::create([
                'username' => $request->username,
                'password' => Hash::make('murid'), // Password statis yang di-hash
                'role' => 'murid', // Set role default untuk murid
            ]);
            // Simpan data ke tabel murid
            Murid::create([
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
        return redirect()->back()->with('success', 'Pendaftaran berhasil');
    }

    public function softDeleteMurid(Request $request)
    {
        $usernames = $request->input('usernames', []);

        if (!is_array($usernames)) {
            $usernames = [$usernames];
        }

        $errors = [];

        foreach ($usernames as $username) {
            // Temukan murid berdasarkan username
            $murid = Murid::where('username', $username)->first();

            if ($murid) {
                // Cek apakah pengguna sedang online
                if ($murid->is_online == 1) {
                    // Jika online, tambahkan pesan error
                    $errors[] = "Pengguna dengan username $username sedang login dan tidak dapat dihapus.";
                } else {
                    // Jika tidak online, lakukan soft delete
                    $murid->delete();
                    // Soft delete data terkait di tabel users
                    $user = User::where('username', $username)->first();
                    if ($user) {
                        $user->delete();
                    }
                }
            }
        }

        // Jika ada error, kembalikan pesan error
        if (!empty($errors)) {
            return response()->json(['error' => $errors], 422);
        }

        return response()->json(['success' => 'Data murid telah dihapus.']);
    }


    public function updateMurid(Request $request, $id_murid)
    {

        // dd($request->all());

        $murid = Murid::with('user')->findOrFail($id_murid);
        $user = $murid->user;

        // dd($murid);

        // Validasi data yang diterima
        $request->validate([
            'nama' => 'nullable|string|max:100',
            'date' => 'nullable|date_format:d-m-Y',
            'alamat' => 'nullable|string',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'telp' => 'nullable|string|max:15',
            'username' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    // Cek jika username sudah ada di tabel murid, users dan ustaz
                    if (
                        User::where('username', $value)->where('username', '!=', Auth::user()->username)->exists() ||
                        Murid::where('username', $value)->where('username', '!=', Auth::user()->username)->exists() ||
                        Ustaz::where('username', $value)->where('username', '!=', Auth::user()->username)->exists() ||
                        Umum::where('username', $value)->where('username', '!=', Auth::user()->username)->exists()
                    ) {
                        $fail('Username sudah digunakan.');
                        return back()->with('error', 'Username sudah digunakan');
                    }
                },
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:40',
                function ($attribute, $value, $fail) {
                    // Cek jika email sudah ada di tabel murid, ustaz
                    if (
                        Murid::where('email', $value)->where('email', '!=', Auth::user()->email)->exists() ||
                        Ustaz::where('email', $value)->where('email', '!=', Auth::user()->email)->exists() ||
                        Umum::where('email', $value)->where('email', '!=', Auth::user()->email)->exists()
                    ) {
                        $fail('Email sudah digunakan.');
                        return back()->with('error', 'Email sudah digunakan');
                    }
                },
            ],
        ]);

        // Persiapkan array update untuk data pengguna
        $userUpdateData = [];
        $isUsernameUpdated = false;

        if ($request->filled('username')) {
            $userUpdateData['username'] = $request->input('username');
            $isUsernameUpdated = true;
        }

        // Update data pengguna
        if (!empty($userUpdateData)) {
            $user->update($userUpdateData);
        }

        // Jika username diupdate, set is_online ke 0
        if ($isUsernameUpdated) {
            $murid->is_online = 0;
            $murid->save();
        }

        // Ambil data kota/kabupaten dari API hanya jika alamat diisi
        $formattedLocation = null;
        if ($request->filled('alamat')) {
            $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
            $cities = $response->json()['data'];

            // Normalisasi dan cari kecocokan lokasi
            $inputLocation = strtolower($request->alamat);
            $matches = array_filter($cities, function ($city) use ($inputLocation) {
                return strtolower($city['lokasi']) === $inputLocation;
            });

            if (count($matches) === 0) {
                return redirect()->back()->with('error', 'Lokasi tidak valid, pilih lokasi yang tersedia.');
            } elseif (count($matches) > 1) {
                return redirect()->back()->with('error', 'Terdapat lebih dari satu lokasi yang cocok, silakan pilih dari daftar.');
            }

            // Ambil lokasi yang cocok
            $match = array_values($matches)[0]; // Mendapatkan elemen pertama
            $validLocation = $match['lokasi'];
            $formattedLocation = ucwords(strtolower($validLocation));
        }

        // Persiapkan array update untuk data murid
        $muridUpdateData = [];
        if ($request->filled('nama')) {
            $muridUpdateData['nama'] = $request->input('nama');
        }

        if ($request->filled('date')) {
            $muridUpdateData['tanggal_lahir'] = Carbon::createFromFormat('d-m-Y', $request->input('date'))->format('Y-m-d');
        }

        if ($request->filled('alamat') && $formattedLocation) {
            $muridUpdateData['alamat'] = $formattedLocation;
        }

        if ($request->filled('gender')) {
            $muridUpdateData['gender'] = $request->input('gender');
        }

        if ($request->filled('telp')) {
            $muridUpdateData['no_telepon'] = $request->input('telp');
        }

        if ($request->filled('email')) {
            $muridUpdateData['email'] = $request->input('email');
        }

        // Update data
        if (!empty($muridUpdateData)) {
            $murid->update($muridUpdateData);
        }

        // Redirect ke halaman profil dengan pesan sukses
        return redirect()->back()->with('success', 'Profil berhasil diubah.');
    }

    public function TrashdataMurid()
    {
        $admin = Auth::user();

        // Ambil data pengguna dengan role 'murid' dan data terkait dari tabel 'murid' yang telah di-soft delete
        $murid = User::where('role', 'murid')
            ->withTrashed() // Mengambil data dari tabel 'users' yang telah di-soft delete
            ->whereHas('murid', function ($query) {
                $query->onlyTrashed(); // Mengambil data dari tabel 'murid' yang telah di-soft delete
            })
            ->with(['murid' => function ($query) {
                $query->onlyTrashed(); // Mengambil data dari tabel 'murid' yang telah di-soft delete
            }])
            ->get();

        try {
            // Ambil data kota/kabupaten dari API
            $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
            $cities = $response->json()['data'];
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, arahkan kembali dengan pesan error
            return view('no_internet')->withErrors(['error' => 'Tidak dapat mengakses data kota/kabupaten. Pastikan Anda terhubung ke internet.']);
        }

        return view('admin.trash.murid', [
            'admin' => $admin,
            'cities' => $cities,
            'editCities' => $cities,
            'murid' => $murid,
        ]);
    }

    public function restoreMurid(Request $request)
    {
        $usernames = $request->input('usernames', []);

        if (!is_array($usernames)) {
            $usernames = [$usernames];
        }

        $errors = [];

        foreach ($usernames as $username) {
            // Temukan murid berdasarkan username
            $murid = Murid::withTrashed()->where('username', $username)->first();

            if ($murid) {
                // Temukan User berdasarkan username
                $user = User::withTrashed()->where('username', $username)->first();

                if ($user) {
                    // Jika User dan murid ditemukan, lakukan restore
                    try {
                        $murid->restore();
                        $user->restore();
                    } catch (\Exception $e) {
                        // Jika terjadi kesalahan saat restore, tambahkan pesan error
                        $errors[] = "Gagal memulihkan data untuk username $username: " . $e->getMessage();
                    }
                } else {
                    $errors[] = "Pengguna dengan username $username tidak ditemukan di tabel users.";
                }
            } else {
                $errors[] = "murid dengan username $username tidak ditemukan di tabel murid.";
            }
        }

        // Jika ada error, kembalikan pesan error
        if (!empty($errors)) {
            return response()->json(['error' => $errors], 422);
        }

        return response()->json(['success' => 'Data murid telah dipulihkan.']);
    }

    public function DeleteMurid(Request $request)
    {
        $usernames = $request->input('usernames', []);

        if (!is_array($usernames)) {
            $usernames = [$usernames];
        }

        $errors = [];

        foreach ($usernames as $username) {
            // Temukan murid berdasarkan username yang telah soft deleted
            $murid = User::onlyTrashed()->where('username', $username)->first();

            if ($murid) {
                // Cek apakah pengguna sedang online
                if ($murid->is_online == 1) {
                    // Jika online, tambahkan pesan error
                    $errors[] = "Pengguna dengan username $username sedang login dan tidak dapat dihapus.";
                } else {
                    // Jika tidak online, lakukan soft delete
                    $murid->forceDelete();
                }
            }
        }

        // Jika ada error, kembalikan pesan error
        if (!empty($errors)) {
            return response()->json(['error' => $errors], 422);
        }

        return response()->json(['success' => 'Data murid telah dihapus.']);
    }


    ///////////////////////////////////////////////////////UMUM///////////////////////////////////////////////////////
    public function dataUmum()
    {
        $admin = Auth::user();
        // Ambil data pengguna dengan role 'umum' dan data terkait dari tabel 'umum'
        $umum = User::where('role', 'umum')
            ->whereHas('umum', function ($query) {
                $query->whereNull('deleted_at'); // Hanya ambil data yang tidak di-soft delete
            })
            ->with('umum') // Mengambil data dari tabel 'umum' terkait
            ->get();
        try {
            // Ambil data kota/kabupaten dari API
            $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
            $cities = $response->json()['data'];
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, arahkan kembali dengan pesan error
            return view('no_internet')->withErrors(['error' => 'Tidak dapat mengakses data kota/kabupaten. Pastikan Anda terhubung ke internet.']);
        }

        // dd($cities);
        return view('admin.umum', [
            'admin' => $admin,
            'cities' => $cities,
            'editCities' => $cities,
            'umum' => $umum,

        ]);
    }

    public function createUmum(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'nama' => 'required|string|max:100',
            'date' => 'required|date_format:d-m-Y',
            'alamat' => 'required|string',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'telp' => 'required|string|max:15',
            'username' => 'required|string|max:20|unique:murid,username|unique:users,username',
            'email' => 'required|string|email|max:40|unique:murid,email',
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
            return redirect()->back()->withErrors(['alamat' => 'Lokasi tidak valid, pilih lokasi yang tersedia.']);
        } elseif (count($matches) > 1) {
            return redirect()->back()->withErrors(['alamat' => 'Terdapat lebih dari satu lokasi yang cocok, silakan pilih dari daftar.']);
        }

        // Ambil lokasi yang cocok
        $match = array_values($matches)[0]; // Mendapatkan elemen pertama
        $validLocation = $match['lokasi'];
        $formattedLocation = ucwords(strtolower($validLocation));

        // Cek apakah username sudah digunakan
        $existingUsername = User::where('username', $request->username)->first();
        if ($existingUsername) {
            return redirect()->back()->withErrors(['username' => 'Username sudah digunakan.']);
        }

        // Cek apakah email sudah digunakan
        $existingEmail = Murid::where('email', $request->email)->exists() || Ustaz::where('email', $request->email)->exists() || Umum::where('email', $request->email)->exists();
        if ($existingEmail) {
            return redirect()->back()->withErrors(['email' => 'Email sudah digunakan.']);
        }

        // Gunakan transaksi database untuk memastikan kedua operasi berikut sukses
        DB::transaction(function () use ($request, $formattedLocation) {
            // Simpan data ke tabel users dengan password statis
            User::create([
                'username' => $request->username,
                'password' => Hash::make('umum'), // Password statis yang di-hash
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
        return redirect()->back()->with('success', 'Pendaftaran berhasil');
    }

    public function softDeleteUmum(Request $request)
    {
        $usernames = $request->input('usernames', []);

        if (!is_array($usernames)) {
            $usernames = [$usernames];
        }

        $errors = [];

        foreach ($usernames as $username) {
            // Temukan Umum berdasarkan username
            $umum = Umum::where('username', $username)->first();

            if ($umum) {
                // Cek apakah pengguna sedang online
                if ($umum->is_online == 1) {
                    // Jika online, tambahkan pesan error
                    $errors[] = "Pengguna dengan username $username sedang login dan tidak dapat dihapus.";
                } else {
                    // Jika tidak online, lakukan soft delete
                    $umum->delete();
                    // Soft delete data terkait di tabel users
                    $user = User::where('username', $username)->first();
                    if ($user) {
                        $user->delete();
                    }
                }
            }
        }

        // Jika ada error, kembalikan pesan error
        if (!empty($errors)) {
            return response()->json(['error' => $errors], 422);
        }

        return response()->json(['success' => 'Data pengguna umum telah dihapus.']);
    }


    public function updateUmum(Request $request, $id_umum)
    {

        // dd($request->all());

        $umum = Umum::with('user')->findOrFail($id_umum);
        $user = $umum->user;

        // dd($umum);

        // Validasi data yang diterima
        $request->validate([
            'nama' => 'nullable|string|max:100',
            'date' => 'nullable|date_format:d-m-Y',
            'alamat' => 'nullable|string',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'telp' => 'nullable|string|max:15',
            'username' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    // Cek jika username sudah ada di tabel murid, users dan ustaz
                    if (
                        User::where('username', $value)->where('username', '!=', Auth::user()->username)->exists() ||
                        Murid::where('username', $value)->where('username', '!=', Auth::user()->username)->exists() ||
                        Ustaz::where('username', $value)->where('username', '!=', Auth::user()->username)->exists() ||
                        Umum::where('username', $value)->where('username', '!=', Auth::user()->username)->exists()
                    ) {
                        $fail('Username sudah digunakan.');
                        return back()->with('error', 'Username sudah digunakan');
                    }
                },
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:40',
                function ($attribute, $value, $fail) {
                    // Cek jika email sudah ada di tabel murid, ustaz
                    if (
                        Murid::where('email', $value)->where('email', '!=', Auth::user()->email)->exists() ||
                        Ustaz::where('email', $value)->where('email', '!=', Auth::user()->email)->exists() ||
                        Umum::where('email', $value)->where('email', '!=', Auth::user()->email)->exists()
                    ) {
                        $fail('Email sudah digunakan.');
                        return back()->with('error', 'Email sudah digunakan');
                    }
                },
            ],
        ]);

        // Persiapkan array update untuk data pengguna
        $userUpdateData = [];
        $isUsernameUpdated = false;

        if ($request->filled('username')) {
            $userUpdateData['username'] = $request->input('username');
            $isUsernameUpdated = true;
        }

        // Update data pengguna
        if (!empty($userUpdateData)) {
            $user->update($userUpdateData);
        }

        // Jika username diupdate, set is_online ke 0
        if ($isUsernameUpdated) {
            $umum->is_online = 0;
            $umum->save();
        }

        // Ambil data kota/kabupaten dari API hanya jika alamat diisi
        $formattedLocation = null;
        if ($request->filled('alamat')) {
            $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
            $cities = $response->json()['data'];

            // Normalisasi dan cari kecocokan lokasi
            $inputLocation = strtolower($request->alamat);
            $matches = array_filter($cities, function ($city) use ($inputLocation) {
                return strtolower($city['lokasi']) === $inputLocation;
            });

            if (count($matches) === 0) {
                return redirect()->back()->with('error', 'Lokasi tidak valid, pilih lokasi yang tersedia.');
            } elseif (count($matches) > 1) {
                return redirect()->back()->with('error', 'Terdapat lebih dari satu lokasi yang cocok, silakan pilih dari daftar.');
            }

            // Ambil lokasi yang cocok
            $match = array_values($matches)[0]; // Mendapatkan elemen pertama
            $validLocation = $match['lokasi'];
            $formattedLocation = ucwords(strtolower($validLocation));
        }

        // Persiapkan array update untuk data murid
        $umumUpdateData = [];
        if ($request->filled('nama')) {
            $umumUpdateData['nama'] = $request->input('nama');
        }

        if ($request->filled('date')) {
            $umumUpdateData['tanggal_lahir'] = Carbon::createFromFormat('d-m-Y', $request->input('date'))->format('Y-m-d');
        }

        if ($request->filled('alamat') && $formattedLocation) {
            $umumUpdateData['alamat'] = $formattedLocation;
        }

        if ($request->filled('gender')) {
            $umumUpdateData['gender'] = $request->input('gender');
        }

        if ($request->filled('telp')) {
            $umumUpdateData['no_telepon'] = $request->input('telp');
        }

        if ($request->filled('email')) {
            $umumUpdateData['email'] = $request->input('email');
        }

        // Update data
        if (!empty($umumUpdateData)) {
            $umum->update($umumUpdateData);
        }

        // Redirect ke halaman profil dengan pesan sukses
        return redirect()->back()->with('success', 'Profil berhasil diubah.');
    }

    public function TrashdataUmum()
    {
        $admin = Auth::user();

        // Ambil data pengguna dengan role 'umum' dan data terkait dari tabel 'umum' yang telah di-soft delete
        $umum = User::where('role', 'umum')
            ->withTrashed() // Mengambil data dari tabel 'users' yang telah di-soft delete
            ->whereHas('umum', function ($query) {
                $query->onlyTrashed(); // Mengambil data dari tabel 'umum' yang telah di-soft delete
            })
            ->with(['umum' => function ($query) {
                $query->onlyTrashed(); // Mengambil data dari tabel 'umum' yang telah di-soft delete
            }])
            ->get();

        try {
            // Ambil data kota/kabupaten dari API
            $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
            $cities = $response->json()['data'];
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, arahkan kembali dengan pesan error
            return view('no_internet')->withErrors(['error' => 'Tidak dapat mengakses data kota/kabupaten. Pastikan Anda terhubung ke internet.']);
        }

        return view('admin.trash.umum', [
            'admin' => $admin,
            'cities' => $cities,
            'editCities' => $cities,
            'umum' => $umum,
        ]);
    }

    public function restoreUmum(Request $request)
    {
        $usernames = $request->input('usernames', []);

        if (!is_array($usernames)) {
            $usernames = [$usernames];
        }

        $errors = [];

        foreach ($usernames as $username) {
            // Temukan Umum berdasarkan username
            $umum = Umum::withTrashed()->where('username', $username)->first();

            if ($umum) {
                // Temukan User berdasarkan username
                $user = User::withTrashed()->where('username', $username)->first();

                if ($user) {
                    // Jika User dan Umum ditemukan, lakukan restore
                    try {
                        $umum->restore();
                        $user->restore();
                    } catch (\Exception $e) {
                        // Jika terjadi kesalahan saat restore, tambahkan pesan error
                        $errors[] = "Gagal memulihkan data untuk username $username: " . $e->getMessage();
                    }
                } else {
                    $errors[] = "Pengguna dengan username $username tidak ditemukan di tabel users.";
                }
            } else {
                $errors[] = "Pengguna Umum dengan username $username tidak ditemukan di tabel umum.";
            }
        }

        // Jika ada error, kembalikan pesan error
        if (!empty($errors)) {
            return response()->json(['error' => $errors], 422);
        }

        return response()->json(['success' => 'Data pengguna umum telah dipulihkan.']);
    }

    public function DeleteUmum(Request $request)
    {
        $usernames = $request->input('usernames', []);

        if (!is_array($usernames)) {
            $usernames = [$usernames];
        }

        $errors = [];

        foreach ($usernames as $username) {
            // Temukan Umum berdasarkan username yang telah soft deleted
            $umum = User::onlyTrashed()->where('username', $username)->first();

            if ($umum) {
                // Cek apakah pengguna sedang online
                if ($umum->is_online == 1) {
                    // Jika online, tambahkan pesan error
                    $errors[] = "Pengguna dengan username $username sedang login dan tidak dapat dihapus.";
                } else {
                    // Jika tidak online, lakukan soft delete
                    $umum->forceDelete();
                }
            }
        }

        // Jika ada error, kembalikan pesan error
        if (!empty($errors)) {
            return response()->json(['error' => $errors], 422);
        }

        return response()->json(['success' => 'Data pengguna umum telah dihapus.']);
    }

    ///////////////////////////////////////////////////////REPORT///////////////////////////////////////////////////////
    public function dataReport()
    {
        $admin = Auth::user();

        // Mengambil semua data dari tabel 'report' beserta relasi 'user' dan 'question'
        $reports = Report::with(['user.murid', 'user.umum', 'user.ustaz', 'question.user.murid', 'question.user.umum', 'question.user.ustaz'])->get();
        //dd($reports);
        return view('admin.report', [
            'admin' => $admin,
            'reports' => $reports,
        ]);
    }

    public function reportDeletePost(Request $request)
    {
        // Mengambil data report dari request
        $reportId = $request->input('report');

        if (!$reportId) {
            // Jika tidak ada report yang dikirim, kembalikan pesan error
            return response()->json(['error' => 'Tidak ada data report yang dikirim.'], 422);
        }

        // Temukan report berdasarkan id_report
        $report = Report::where('id_report', $reportId)->first();

        if ($report) {
            try {
                // Temukan semua laporan lain yang memiliki id_question yang sama
                $relatedReports = Report::where('id_question', $report->id_question)->get();

                // Hapus semua laporan terkait
                foreach ($relatedReports as $relatedReport) {
                    $relatedReport->forceDelete();
                }

                // Hapus data question yang memiliki id_question yang sama
                $question = Question::where('id_question', $report->id_question)->first();
                if ($question) {
                    $question->forceDelete();
                }

                return response()->json(['success' => 'Data post dan laporan terkait telah dihapus.']);
            } catch (\Exception $e) {
                // Jika ada error saat menghapus, kembalikan pesan error
                return response()->json(['error' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()], 500);
            }
        } else {
            // Jika tidak menemukan report, kembalikan pesan error
            return response()->json(['error' => 'Data report tidak ditemukan.'], 404);
        }
    }



    public function reportDeleteReport(Request $request)
    {
        $reports = $request->input('reports', []);

        if (!is_array($reports)) {
            $reports = [$reports];
        }

        $errors = [];

        foreach ($reports as $report) {
            // Temukan Umum berdasarkan report yang telah soft deleted
            $report = Report::where('id_report', $report)->first();

            if ($report) {

                $report->forceDelete();
            }
        }

        // Jika ada error, kembalikan pesan error
        if (!empty($errors)) {
            return response()->json(['error' => $errors], 422);
        }

        return response()->json(['success' => 'Data post telah dihapus.']);
    }

    ///////////////////////////////////////////////////////IKLAN///////////////////////////////////////////////////////
    public function dataIklan()
    {
        $admin = Auth::user();
        // Ambil data iklan dan urutkan berdasarkan data terbaru
        $iklan = Iklan::orderBy('created_at', 'desc')->get();


        // dd($cities);
        return view('admin.iklan', [
            'admin' => $admin,
            'iklan' => $iklan,

        ]);
    }

    public function createIklan(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'judul' => 'required|string',
            'deskripsi' => 'required|string',
            'linkIklan' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:20480'
        ]);


        // Membuat objek baru
        $iklan = new Iklan();

        // Menetapkan nilai atribut sesuai dengan data yang diterima dari formulir
        $iklan->judul = $request->judul;
        $iklan->deskripsi = $request->deskripsi;
        $iklan->linkIklan = $request->linkIklan;

        // Mengunggah gambar dan menetapkan nilainya
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageData = file_get_contents($image->getRealPath());
            $iklan->gambar = base64_encode($imageData);
        }

        // Memeriksa data yang diterima
        // dd($iklan);

        // Menyimpan data ke dalam database
        $iklan->save();

        return redirect()->back()->with('success', 'Iklan berhasil ditambahkan');
    }

    public function softDeleteIklan(Request $request)
    {
        $iklans = $request->input('iklans', []);

        if (!is_array($iklans)) {
            $iklans = [$iklans];
        }

        $errors = [];

        foreach ($iklans as $id) {
            // Temukan Iklan berdasarkan id
            $iklan = Iklan::where('id_iklan', $id)->first();
            $iklan->delete();
        }

        return response()->json(['success' => 'Data iklan telah dihapus.']);
    }


    public function updateIklan(Request $request, $id_iklan)
    {
        // Validasi data yang diterima
        $request->validate([
            'judul' => 'required|string',
            'deskripsi' => 'required|string',
            'linkIklan' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:20480'
        ]);

        $iklan = Iklan::find($id_iklan);

        if (!$iklan) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
        //dd($request->all());

        // Persiapkan array update untuk data 
        $editIklan = [];
        if ($request->filled('judul')) {
            $editIklan['judul'] = $request->input('judul');
        }

        if ($request->filled('deskripsi')) {
            $editIklan['deskripsi'] = $request->input('deskripsi');
        }

        if ($request->filled('linkIklan')) {
            $editIklan['linkIklan'] = $request->input('linkIklan');
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageData = file_get_contents($image->getRealPath());
            $editIklan['gambar'] = base64_encode($imageData);
        }

        //dd($editIklan);

        // Update data 
        if (!empty($editIklan)) {
            $iklan->update($editIklan);
        }

        return redirect()->back()->with('success', 'Berhasil diubah');
    }

    public function TrashdataIklan()
    {
        $admin = Auth::user();

        $iklan = Iklan::onlyTrashed()->get();

        return view('admin.trash.iklan', [
            'admin' => $admin,
            'iklan' => $iklan,
        ]);
    }

    public function restoreIklan(Request $request)
    {
        $iklans = $request->input('iklans', []);

        if (!is_array($iklans)) {
            $iklans = [$iklans];
        }

        $errors = [];

        foreach ($iklans as $id) {
            // Temukan Iklan
            $iklan = Iklan::withTrashed()->where('id_iklan', $id)->first();

            if ($iklan) {
                try {
                    $iklan->restore();
                } catch (\Exception $e) {
                    // Jika terjadi kesalahan saat restore, tambahkan pesan error
                    $errors[] = "Gagal memulihkan data  iklan: " . $e->getMessage();
                }
            }
        }

        // Jika ada error, kembalikan pesan error
        if (!empty($errors)) {
            return response()->json(['error' => $errors], 422);
        }

        return response()->json(['success' => 'Data iklan telah dipulihkan.']);
    }

    public function DeleteIklan(Request $request)
    {
        $iklans = $request->input('iklans', []);

        if (!is_array($iklans)) {
            $iklans = [$iklans];
        }

        $errors = [];

        foreach ($iklans as $id) {
            // Temukan Iklan b
            $iklan = Iklan::withTrashed()->where('id_iklan', $id)->first();
            $iklan->forceDelete();
        }

        return response()->json(['success' => 'Data iklan telah dihapus.']);
    }
}
