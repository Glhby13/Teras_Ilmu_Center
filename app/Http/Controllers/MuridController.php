<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Murid;
use App\Models\Ustaz;
use App\Models\Umum;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Follow;
use App\Models\Iklan;
use Carbon\Carbon;

class MuridController extends Controller
{
    public function index($kategori = null)
    {
        // Ambil pengguna yang sedang login
        $user = Auth::user();

        // Ambil data murid yang terkait dengan pengguna yang sedang login
        $murid = $user->murid;

        $sort = request('sort', 'newest'); // Ambil parameter sort dari request, default 'newest'

        $question = Question::with([
            'user.ustaz',
            'user.murid',
            'user.umum',
            'answer.question',
            'answer.replyTouser.ustaz',
            'answer.replyTouser.murid',
            'answer.replyTouser.umum'
        ])
            ->withCount([
                'vote as upvotes_count' => function ($query) {
                    $query->where('vote_type', 'UpVote');
                },
                'vote as downvotes_count' => function ($query) {
                    $query->where('vote_type', 'DownVote');
                }
            ])
            ->with(['vote' => function ($query) use ($user) {
                $query->where('username', $user->username);
            }])
            ->when($kategori, function ($query, $kategori) {
                return $query->where('kategori', $kategori);
            })
            ->when($sort === 'upvotes', function ($query) {
                return $query->orderBy('upvotes_count', 'DESC');
            })
            ->when($sort === 'downvotes', function ($query) {
                return $query->orderBy('downvotes_count', 'DESC');
            })
            ->when($sort === 'newest', function ($query) {
                return $query->orderBy('created_at', 'DESC');
            })
            ->when($sort === 'oldest', function ($query) {
                return $query->orderBy('created_at', 'ASC');
            })
            ->get();

        $follow = Follow::all();
        $iklan = Iklan::all();
        //dd($follow);

        // Kirim data murid ke view
        return view('home', [
            'user' => $user,
            'data' => $murid,
            'question' => $question,
            'selectedKategori' => $kategori,
            'follow' => $follow,
            'iklan' => $iklan
        ]);
    }

    public function profile()
    {
        // Set locale untuk Carbon
        \Carbon\Carbon::setLocale('id');

        // Ambil pengguna yang sedang login
        $user = Auth::user();

        // Ambil data murid yang terkait dengan pengguna yang sedang login
        $murid = $user->murid;

        // // Cek data sebelum mengirim ke view
        // dd([
        //     'user' => $user,
        //     'data' => $murid,
        // ]);

        // Format tanggal
        $created_at_formatted = \Carbon\Carbon::parse($murid->created_at)->translatedFormat('d F Y');

        try {
            // Ambil data kota/kabupaten dari API
            $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
            $cities = $response->json()['data'];
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, arahkan kembali dengan pesan error
            return view('no_internet')->withErrors(['error' => 'Tidak dapat mengakses data kota/kabupaten. Pastikan Anda terhubung ke internet.']);
        }

        // dd($cities);
        // Mengambil jumlah answers, questions, followers, dan followings
        $answers = $user->answer()->count() ?? 0;
        $questions = $user->question()->count() ?? 0;
        $followers = $user->followings()->count() ?? 0;
        $followings = $user->followers()->count() ?? 0;

        // Kirim data murid ke view
        return view('profile', [
            'user' => $user,
            'data' => $murid,
            'created_at_formatted' => $created_at_formatted,
            'cities' => $cities,
            'questions' => $questions,
            'answers' => $answers,
            'followers' => $followers,
            'followings' => $followings,
        ]);
    }

    public function viewprofile($username)
    {
        // Ambil pengguna yang sedang login
        $auth = Auth::user();

        // Cek apakah user ditemukan
        if (!$auth) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        // Cek role user dan ambil data yang sesuai
        $data = null;
        if ($auth->role == 'murid') {
            // Ambil data murid berdasarkan relasi
            $data = $auth->murid;
        } elseif ($auth->role == 'ustaz') {
            // Ambil data ustaz berdasarkan relasi
            $data = $auth->ustaz;
        } elseif ($auth->role == 'umum') {
            // Ambil data umum berdasarkan relasi
            $data = $auth->umum;
        }

        // Ambil user berdasarkan username
        $user = User::where('username', $username)->first();

        // Cek apakah user ditemukan
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        // Cek role user dan ambil data yang sesuai
        $profile = null;
        if ($user->role == 'murid') {
            // Ambil data murid berdasarkan relasi
            $profile = $user->murid;
        } elseif ($user->role == 'ustaz') {
            // Ambil data ustaz berdasarkan relasi
            $profile = $user->ustaz;
        } elseif ($user->role == 'umum') {
            // Ambil data umum berdasarkan relasi
            $profile = $user->umum;
        }

        // Format tanggal
        $created_at_formatted = \Carbon\Carbon::parse($profile->created_at)->translatedFormat('d F Y');
        try {
            // Ambil data kota/kabupaten dari API
            $response = Http::get('https://api.myquran.com/v2/sholat/kota/semua');
            $cities = $response->json()['data'];
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, arahkan kembali dengan pesan error
            return view('no_internet')->withErrors(['error' => 'Tidak dapat mengakses data kota/kabupaten. Pastikan Anda terhubung ke internet.']);
        }

        // dd($cities);
        // Mengambil jumlah answers, questions, followers, dan followings
        $answers = $user->answer()->count() ?? 0;
        $questions = $user->question()->count() ?? 0;
        $followers = $user->followings()->count() ?? 0;
        $followings = $user->followers()->count() ?? 0;

        // Cek apakah data profile ditemukan
        if (!$profile) {
            return redirect()->back()->with('error', 'Data profil tidak ditemukan.');
        }

        //dd($profile);

        // Return view dengan data profile
        return view('viewprofile', [
            'user' => $user,
            'data' => $data,
            'datauser' => $profile,
            'created_at_formatted' => $created_at_formatted,
            'cities' => $cities,
            'questions' => $questions,
            'answers' => $answers,
            'followers' => $followers,
            'followings' => $followings,
        ]);
    }

    public function updatePassword(Request $request)
    {
        //dd($request->all());

        // Validasi input
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed', // password confirmation field is required
        ]);

        // Ambil user yang sedang login
        $user = Auth::user();

        // Cek apakah password saat ini sesuai dengan yang ada di database
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        // Update password baru
        $user->password = Hash::make($request->password);
        $user->save();

        // Redirect atau berikan pesan sukses
        return back()->with('success', 'Password berhasil diupdate.');
    }

    public function updateProfile(Request $request)
    {
        // dd($request->all());
        $user = Auth::user();
        $murid = $user->murid;

        // Jika permintaan untuk menghapus gambar
        if ($request->has('action') && $request->action === 'delete_image') {
            // Hapus gambar dari database
            $murid->gambar = null;
            $murid->save();
        }

        // Validasi data yang diterima
        $request->validate([
            'nama' => 'nullable|string|max:100',
            'date' => 'nullable|date_format:d-m-Y',
            'alamat' => 'nullable|string',
            'pekerjaan' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:50',
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
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

        if ($request->filled('pendidikan')) {
            $muridUpdateData['pendidikan'] = $request->input('pendidikan');
        }

        if ($request->filled('pekerjaan')) {
            $muridUpdateData['pekerjaan'] = $request->input('pekerjaan');
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

        // if ($request->hasFile('image')) {
        //     $muridUpdateData['gambar'] = $this->uploadImage($request->file('image'), $murid->gambar);
        // }

        // Mengunggah gambar dan menetapkan nilainya
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageData = file_get_contents($image->getRealPath());
            $muridUpdateData['gambar'] = base64_encode($imageData);
        }

        // Update data murid
        if (!empty($muridUpdateData)) {
            $murid->update($muridUpdateData);
        }

        // Redirect ke halaman profil dengan pesan sukses
        return redirect()->back()->with('success', 'Profil berhasil diubah.');
    }


    // private function uploadImage($image, $base64Image)
    // {
    //     if ($image) {
    //         // Mengubah gambar menjadi base64
    //         $base64Image = base64_encode(file_get_contents($image->getRealPath()));
    //         // dd($base64Image);
    //     }
    //     return $base64Image; // Return existing image if no new image is uploaded
    // }

    public function updateDeskripsi(Request $request)
    {
        // dd($request->all());

        $user = Auth::user();
        $murid = $user->murid;

        if ($request->has('action') && $request->action === 'delete_deskripsi') {
            // Hapus gambar dari database
            $murid->deskripsi = null;
            $murid->save();

            return redirect()->back()->with('success', 'Deskripsi berhasil dihapus.');
        }

        // Validasi data yang diterima
        $request->validate([
            'deskripsi' => 'nullable|string'
        ]);

        // Ambil pengguna yang sedang login
        $user = Auth::user();

        // Ambil data murid yang terkait dengan pengguna yang sedang login
        $murid = $user->murid;

        // Persiapkan array update untuk data murid
        $muridUpdateData = [];
        if ($request->filled('deskripsi')) {
            $muridUpdateData['deskripsi'] = $request->input('deskripsi');
        }
        // Update data murid
        if (!empty($muridUpdateData)) {
            $murid->update($muridUpdateData);
        }

        // Redirect ke halaman profil dengan pesan sukses
        return redirect()->back()->with('success', 'Deskripsi berhasil diubah');
    }
}
