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
use Carbon\Carbon;

class KitabController extends Controller
{
    public function index()
    {
        // Ambil pengguna yang sedang login
        $user = Auth::user();

        // Inisialisasi variabel $data dan $cityId
        $data = null;
        $cityId = null;

        // Periksa peran pengguna dan ambil data yang sesuai
        if ($user->role === 'murid') {
            $data = $user->murid; // Ambil data murid
        } elseif ($user->role === 'ustaz') {
            $data = $user->ustaz; // Ambil data ustaz
        } elseif ($user->role === 'umum') {
            $data = $user->umum; // Ambil data umum
        }

        // Ambil alamat dari data pengguna
        $alamat = $data->alamat ?? '';

        // dd($data);

        if (!empty($alamat)) {
            try {
                // Langkah 1: Ambil ID kota dari alamat
                $encodedAlamat = str_replace(' ', '%20', $alamat); // Encode alamat untuk URL
                $citySearchUrl = "https://api.myquran.com/v2/sholat/kota/cari/{$encodedAlamat}";
                $response = Http::get($citySearchUrl);
                $cityData = $response->json();

                if ($cityData['status'] && !empty($cityData['data'])) {
                    // Ambil ID kota dari data API
                    $cityId = $cityData['data'][0]['id'];
                }

                // dd($cityData);

                // Langkah 2: Ambil jadwal sholat dengan ID kota
                if ($cityId) {
                    $date = now()->format('Y-m-d'); // Format tanggal saat ini
                    $prayerScheduleUrl = "https://api.myquran.com/v2/sholat/jadwal/{$cityId}/{$date}";
                    $prayerResponse = Http::get($prayerScheduleUrl);
                    $prayerData = $prayerResponse->json();

                    // Memasukkan data jadwal sholat ke dalam view
                    $prayerSchedule = $prayerData['data'] ?? null;
                }
            } catch (\Exception $e) {
                // Jika terjadi kesalahan, arahkan kembali dengan pesan error
                return view('no_internet')->withErrors(['error' => 'Tidak dapat mengakses data dari API. Pastikan Anda terhubung ke internet.']);
            }
        }

        // Kembalikan view dengan data yang sesuai
        return view('kitab.index', [
            'user' => $user,
            'data' => $data,
            'prayerSchedule' => $prayerSchedule ?? null, // Menyertakan jadwal sholat jika tersedia
        ]);
    }
}
