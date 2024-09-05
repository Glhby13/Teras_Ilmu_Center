<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\Report;
use App\Models\Vote;



class QuestionController extends Controller
{
    public function create(Request $request)
    {
        //dd($request->all());
        // Validasi data yang diterima
        $request->validate([
            'kategori' => 'required|in:Sholat,Nikah,Puasa,Zakat',
            'deskripsi' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:20480'
        ]);

        // Ambil pengguna yang sedang login
        $user = Auth::user();

        // Membuat objek baru
        $question = new Question();

        // Menetapkan nilai atribut sesuai dengan data yang diterima dari formulir
        $question->username = $user->username;
        $question->kategori = $request->kategori;
        $question->deskripsi = $request->deskripsi;

        // Mengunggah gambar dan menetapkan nilainya
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageData = file_get_contents($image->getRealPath());
            $question->gambar = base64_encode($imageData);
        }

        // Memeriksa data yang diterima
        // dd($question);

        // Menyimpan data ke dalam database
        $question->save();

        return redirect()->back()->with('success', 'Berhasil diunggah');
    }

    public function edit(Request $request, $id_question)
    {
        // Validasi data yang diterima
        $request->validate([
            'kategori' => 'nullable|in:Sholat,Nikah,Puasa,Zakat',
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:20480'
        ]);

        $question = Question::find($id_question);

        if (!$question) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
        // dd($request->all());

        // Persiapkan array update untuk data 
        $editQuestion = [];
        if ($request->filled('kategori')) {
            $editQuestion['kategori'] = $request->input('kategori');
        }

        if ($request->filled('deskripsi')) {
            $editQuestion['deskripsi'] = $request->input('deskripsi');
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageData = file_get_contents($image->getRealPath());
            $editQuestion['gambar'] = base64_encode($imageData);
        }

        // dd($editQuestion);

        // Update data 
        if (!empty($editQuestion)) {
            $question->update($editQuestion);
        }

        return redirect()->back()->with('success', 'Berhasil diubah');
    }

    public function delete($id_question)
    {

        Question::where('id_question', $id_question)->forceDelete();

        return redirect()->back()->with('success', 'Berhasil dihapus');
    }

    public function vote(Request $request, $id_question)
    {
        // Debugging untuk melihat data request
        //dd($request->all());

        // Ambil data dari request
        $voteType = $request->input('vote_type');
        $username = Auth::user()->username;

        // Cek apakah sudah ada vote untuk question ini dari user ini
        $existingVote = Vote::where('id_question', $id_question)
            ->where('username', $username)
            ->first();

        if ($existingVote) {
            // Jika vote_type sama dengan yang ada, hapus vote
            if ($existingVote->vote_type === $voteType) {
                $existingVote->delete();
            } else {
                // Jika vote_type berbeda, update vote_type
                $existingVote->vote_type = $voteType;
                $existingVote->save();
            }
        } else {
            // Jika belum ada, tambahkan vote baru
            Vote::create([
                'id_question' => $id_question,
                'username' => $username,
                'vote_type' => $voteType
            ]);
        }

        // Redirect atau kembali ke halaman sebelumnya
        return redirect()->back();
    }

    public function reportQuestion(Request $request, $id_question)
    {
        // dd($request->all());
        // Ambil pengguna yang sedang login
        $user = Auth::user();

        // Membuat objek baru
        $report = new Report();

        // Menetapkan nilai atribut sesuai dengan data yang diterima dari formulir
        $report->username = $user->username;
        $report->id_question = $id_question;
        $report->deskripsi = $request->reason;

        // Memeriksa data yang diterima
        //dd($report);

        // Menyimpan data ke dalam database
        $report->save();

        return redirect()->back()->with('success', 'Berhasil dilaporkan');
    }
}
