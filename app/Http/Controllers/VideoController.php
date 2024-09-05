<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Video;

class VideoController extends Controller
{
    public function index()
    {
        // Ambil pengguna yang sedang login
        $user = Auth::user();

        // Inisialisasi variabel $data
        $data = null;

        // Periksa peran pengguna dan ambil data yang sesuai
        if ($user->role === 'murid') {
            $data = $user->murid; // Ambil data murid
        } elseif ($user->role === 'ustaz') {
            $data = $user->ustaz; // Ambil data ustaz
        } elseif ($user->role === 'umum') {
            $data = $user->umum; // Ambil data umum
        }

        // Ambil data video dan urutkan berdasarkan data terbaru
        $video = Video::orderBy('created_at', 'desc')->get();
        // $video = Video::all();

        return view('video.video', [
            'user' => $user,
            'data' => $data,
            'video' => $video,
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'judulVideo' => 'required|string|max:255',
            'deskripsiVideo' => 'required|string',
            'linkVideo' => 'required|url',
        ]);

        // Regex untuk memvalidasi URL YouTube
        $youtubeRegex = '/^(https?:\/\/)?(www\.youtube\.com|youtu\.be)\/.+$/i';

        if (!preg_match($youtubeRegex, $request->input('linkVideo'))) {
            return redirect()->back()->with('error', 'URL YouTube tidak valid');
        }

        // // Ambil pengguna yang sedang login
        // $user = Auth::user();

        // Jika semua validasi lolos, simpan video (di sini hanya contoh, implementasi penyimpanan bisa ditambahkan)
        Video::create([
            'judulVideo' => $request->judulVideo,
            'deskripsiVideo' => $request->deskripsiVideo,
            'linkVideo' => $request->linkVideo,
        ]);

        return redirect()->back()->with('success', 'Video berhasil diunggah');
    }

    public function edit(Request $request, $id_video)
    {
        // dd($request->all());
        // Validasi data yang diterima
        $request->validate([
            'editjudulVideo' => 'required|string|max:255',
            'editdeskripsiVideo' => 'required|string',
            'editlinkVideo' => 'required|url',
        ]);

        $video = Video::find($id_video);

        if (!$video) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Regex untuk memvalidasi URL YouTube
        $youtubeRegex = '/^(https?:\/\/)?(www\.youtube\.com|youtu\.be)\/.+$/i';

        if (!preg_match($youtubeRegex, $request->input('editlinkVideo'))) {
            return redirect()->back()->with('error', 'URL YouTube tidak valid');
        }

        $editVideo = [];
        if ($request->filled('editjudulVideo')) {
            $editVideo['judulVideo'] = $request->input('editjudulVideo');
        }

        if ($request->filled('editdeskripsiVideo')) {
            $editVideo['deskripsiVideo'] = $request->input('editdeskripsiVideo');
        }

        if ($request->filled('editlinkVideo')) {
            $editVideo['linkVideo'] = $request->input('editlinkVideo');
        }

        // dd($editQuestion);

        // Update data
        if (!empty($editVideo)) {
            $video->update($editVideo);
        }

        return redirect()->back()->with('success', 'Berhasil diubah');
    }

    public function delete($id_video)
    {

        Video::where('id_video', $id_video)->forceDelete();

        return redirect()->back()->with('success', 'Berhasil dihapus');
    }
}
