<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sound;
use Illuminate\Support\Facades\Storage;

class SoundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Sound::all()->map(function ($sound) {
            $sound->url = Storage::disk('s3')->url($sound->path);
            return $sound;
        });
    }
}
