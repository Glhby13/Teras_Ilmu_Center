<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Murid;
use App\Models\Ustaz;
use App\Models\Umum;
use App\Models\Livechat;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class LivechatController extends Controller
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

        // Ambil daftar ustaz yang sedang online
        $ustazOnline = Ustaz::where('is_online', true)->get();

        // Cek apakah ada sesi live chat yang aktif untuk pengguna saat ini
        $hasActiveSession = Livechat::where('username', $user->username)->exists();

        $livechat = Livechat::where('username', $user->username)->first();

        // Ambil pesan-pesan yang terkait dengan livechat yang aktif, termasuk user yang mengirim pesan
        $messages = $livechat ? $livechat->message()->with('user')->get() : collect();

        // dd($livechat);
        return view('livechat.murid.chat', [
            'user' => $user,
            'data' => $data,
            'ustazOnline' => $ustazOnline,
            'hasActiveSession' => $hasActiveSession,
            'livechat' => $livechat,
            'messages' => $messages,
        ]);
    }

    public function startSession()
    {
        $user = Auth::user();

        // Mulai sesi live chat baru
        Livechat::create([
            'username' => $user->username,
        ]);

        return redirect()->back();
    }

    public function sendMessage(Request $request, $id_livechat)
    {
        dd($request->all());

        $user = Auth::user();
        $request->validate([
            'message' => 'required|string',
        ]);

        // Membuat objek baru
        $message = new Message();

        // Menetapkan nilai atribut sesuai dengan data yang diterima dari formulir
        $message->username = $user->username;
        $message->id_livechat = $id_livechat;
        $message->message = $request->message;
        $message->save();

        return redirect()->back();
    }

    public function fetchMessages($id_livechat)
    {
        $livechat = Livechat::where('id_livechat', $id_livechat)->first();
        $messages = $livechat ? $livechat->message()->with('user')->get() : collect();

        $html = view('livechat.murid.messages', ['messages' => $messages])->render();

        return response()->json(['html' => $html]);
    }

    public function sendMessageAjax(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'message' => 'required|string',
            'id_livechat' => 'required|integer',
        ]);

        $message = new Message();
        $message->username = $user->username;
        $message->id_livechat = $request->id_livechat;
        $message->message = $request->message;
        $message->save();

        return response()->json([
            'message' => $message->message,
            'username' => $user->username,
            'timestamp' => $message->created_at->format('H:i'),
        ]);
    }

    public function getUstazOnline()
    {
        // Ambil daftar ustaz yang sedang online
        $ustazOnline = Ustaz::where('is_online', true)->get();

        // Cek apakah ada sesi live chat yang aktif untuk pengguna saat ini
        $user = Auth::user();
        $hasActiveSession = Livechat::where('username', $user->username)->exists();

        // Mengembalikan view partials yang akan di-append melalui AJAX
        $ustazOnlineHtml = view('livechat.murid.ustazOnline', compact('ustazOnline', 'hasActiveSession'))->render();

        return response()->json(['ustazOnlineHtml' => $ustazOnlineHtml]);
    }


    ///////////////////////////////////////////////////////UMUM///////////////////////////////////////////////////////
    public function Umumindex()
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

        // Ambil daftar ustaz yang sedang online
        $ustazOnline = Ustaz::where('is_online', true)->get();

        // Cek apakah ada sesi live chat yang aktif untuk pengguna saat ini
        $hasActiveSession = Livechat::where('username', $user->username)->exists();

        $livechat = Livechat::where('username', $user->username)->first();

        // Ambil pesan-pesan yang terkait dengan livechat yang aktif, termasuk user yang mengirim pesan
        $messages = $livechat ? $livechat->message()->with('user')->get() : collect();

        // dd($livechat);
        return view('livechat.umum.chat', [
            'user' => $user,
            'data' => $data,
            'ustazOnline' => $ustazOnline,
            'hasActiveSession' => $hasActiveSession,
            'livechat' => $livechat,
            'messages' => $messages,
        ]);
    }

    public function UmumstartSession()
    {
        $user = Auth::user();

        // Mulai sesi live chat baru
        Livechat::create([
            'username' => $user->username,
        ]);

        return redirect()->back();
    }

    public function UmumsendMessage(Request $request, $id_livechat)
    {
        // dd($request->all());

        $user = Auth::user();
        $request->validate([
            'message' => 'required|string',
        ]);

        // Membuat objek baru
        $message = new Message();

        // Menetapkan nilai atribut sesuai dengan data yang diterima dari formulir
        $message->username = $user->username;
        $message->id_livechat = $id_livechat;
        $message->message = $request->message;
        $message->save();

        return redirect()->back();
    }

    public function UmumfetchMessages($id_livechat)
    {
        $livechat = Livechat::where('id_livechat', $id_livechat)->first();
        $messages = $livechat ? $livechat->message()->with('user')->get() : collect();

        $html = view('livechat.umum.messages', ['messages' => $messages])->render();

        return response()->json(['html' => $html]);
    }

    public function UmumsendMessageAjax(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'message' => 'required|string',
            'id_livechat' => 'required|integer',
        ]);

        $message = new Message();
        $message->username = $user->username;
        $message->id_livechat = $request->id_livechat;
        $message->message = $request->message;
        $message->save();

        return response()->json([
            'message' => $message->message,
            'username' => $user->username,
            'timestamp' => $message->created_at->format('H:i'),
        ]);
    }

    public function UmumgetUstazOnline()
    {
        // Ambil daftar ustaz yang sedang online
        $ustazOnline = Ustaz::where('is_online', true)->get();

        // Cek apakah ada sesi live chat yang aktif untuk pengguna saat ini
        $user = Auth::user();
        $hasActiveSession = Livechat::where('username', $user->username)->exists();

        // Mengembalikan view partials yang akan di-append melalui AJAX
        $ustazOnlineHtml = view('livechat.umum.ustazOnline', compact('ustazOnline', 'hasActiveSession'))->render();

        return response()->json(['ustazOnlineHtml' => $ustazOnlineHtml]);
    }

    ///////////////////////////////////////////////////////USTAZ///////////////////////////////////////////////////////

    public function indexUstaz()
    {
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


        $livechat = Livechat::with([
            'user.murid',
            'user.umum',
        ])->orderBy('created_at', 'desc')->get();

        $countlivechat = Livechat::with([
            'user.murid',
            'user.umum',
        ])->orderBy('created_at', 'desc')->get();
        // dd($livechat);
        // Return view untuk AJAX request
        if (request()->ajax()) {
            return view('livechat.ustaz.livechat_list', compact('livechat'))->render();
        }

        return view('livechat.ustaz.index', [
            'user' => $user,
            'data' => $data,
            'livechat' => $livechat,
            'countlivechat' => $countlivechat,
        ]);
    }


    public function chatUstaz($id_livechat)
    {
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

        $livechat = Livechat::where('id_livechat', $id_livechat)->first();

        $messages = Message::where('id_livechat', $id_livechat)
            ->with('user')
            ->get();

        //dd($messages);

        return view('livechat.ustaz.chat', [
            'user' => $user,
            'data' => $data,
            'livechat' => $livechat,
            'messages' => $messages,
        ]);
    }

    public function fetchMessagesUstaz($id_livechat)
    {
        $livechat = Livechat::where('id_livechat', $id_livechat)->first();
        $messages = $livechat ? $livechat->message()->with('user')->get() : collect();
        $countlivechat = Livechat::orderBy('created_at', 'desc')->get();

        $html = view('livechat.ustaz.messages', ['messages' => $messages, 'countlivechat' => $countlivechat])->render();

        return response()->json(['html' => $html]);
    }

    public function sendMessageAjaxUstaz(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'message' => 'required|string',
            'id_livechat' => 'required|integer',
        ]);

        $message = new Message();
        $message->username = $user->username;
        $message->id_livechat = $request->id_livechat;
        $message->message = $request->message;
        $message->save();

        return response()->json([
            'message' => $message->message,
            'username' => $user->username,
            'timestamp' => $message->created_at->format('H:i'),
        ]);
    }
}
