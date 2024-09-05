<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Murid;
use App\Models\Ustaz;
use App\Models\Umum;
use App\Models\Question;
use App\Models\Answer;

class AnswerController extends Controller
{
    public function create(Request $request, $id_question)
    {
        //dd($request->all());

        $request->validate([
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:20480'
        ]);

        $user = Auth::user();

        $answer = new Answer();

        $createAnswer = [];

        $createAnswer['username'] = $user->username;
        $createAnswer['id_question'] = $id_question;

        if ($request->filled('id_parent')) {
            $createAnswer['id_parent'] = $request->input('id_parent');
        }

        if ($request->filled('replyTo')) {
            $createAnswer['replyTo'] = $request->input('replyTo');
        }

        if ($request->filled('deskripsi')) {
            $createAnswer['deskripsi'] = $request->input('deskripsi');
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageData = file_get_contents($image->getRealPath());
            $createAnswer['gambar'] = base64_encode($imageData);
        }

        //dd($createAnswer);


        // Update data
        if (!empty($createAnswer)) {
            $answer->create($createAnswer);
        }

        return redirect()->back()->with('success', 'Berhasil diunggah');
    }

    public function edit(Request $request, $id_answer)
    {
        //dd($request->all());
        // Validasi data yang diterima
        $request->validate([
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:20480'
        ]);

        $answer = Answer::find($id_answer);

        if (!$answer) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }




        // dd($answer);
        // dd($request->all());

        // Persiapkan array update untuk data 
        $editAnswer = [];
        if ($request->filled('deskripsi')) {
            $editAnswer['deskripsi'] = $request->input('deskripsi');
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageData = file_get_contents($image->getRealPath());
            $editAnswer['gambar'] = base64_encode($imageData);
        }

        // dd($editAnswer);

        // Update data 
        if (!empty($editAnswer)) {
            $answer->update($editAnswer);
        }

        // Jika permintaan untuk menghapus gambar
        if ($request->has('action') && $request->action === 'delete_image') {
            // Hapus gambar dari database
            $answer->gambar = null;
            $answer->save();
        }

        return redirect()->back()->with('success', 'Berhasil diubah');
    }

    public function delete($id_answer)
    {

        // Hapus semua balasan yang terkait dengan id_parent yang sama dengan id_answer
        Answer::where('id_parent', $id_answer)->forceDelete();

        // Hapus data utama
        Answer::where('id_answer', $id_answer)->forceDelete();

        return redirect()->back()->with('success', 'Berhasil dihapus');
    }
}
