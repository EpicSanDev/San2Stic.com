<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Sound;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalSounds' => Sound::count(),
            'totalLikes' => \DB::table('likes')->count(), // Direct query for pivot table
            'totalComments' => Comment::count(),
        ];

        return view('home', compact('stats'));
    }
}