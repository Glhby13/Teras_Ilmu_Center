<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Follow;

class FollowController extends Controller
{
    public function follow(Request $request)
    {
        //dd($request->all());

        $validated = $request->validate([
            'follower' => 'required|string|max:20',
            'following' => 'required|string|max:20',
        ]);
        // Create a new follow record
        Follow::create([
            'follower' => $validated['follower'],
            'following' => $validated['following'],
        ]);

        // Redirect or return response
        return redirect()->back()->with('success', 'Followed successfully!');
    }

    public function unfollow(Request $request)
    {
        // Validasi input
        $request->validate([
            'follower' => 'required|string',
            'following' => 'required|string',
        ]);

        // Temukan dan hapus follow
        $follow = Follow::where('follower', $request->input('follower'))
            ->where('following', $request->input('following'))
            ->first();

        if ($follow) {
            $follow->delete();
            return redirect()->back()->with('success', 'Unfollowed successfully.');
        }

        return redirect()->back()->with('error', 'Follow relationship not found.');
    }
}
