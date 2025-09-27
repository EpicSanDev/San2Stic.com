<?php

namespace App\Livewire;

use App\Models\Sound;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LikeButton extends Component
{
    public Sound $sound;
    public bool $isLiked;
    public int $likeCount;

    public function mount(Sound $sound)
    {
        $this->sound = $sound;
        $this->updateLikeStatus();
    }

    public function toggleLike()
    {
        if (Auth::guest()) {
            return redirect()->route('login');
        }

        Auth::user()->likes()->toggle($this->sound);

        $this->updateLikeStatus();
    }

    public function updateLikeStatus()
    {
        // We need to refresh the relationship count
        $this->likeCount = $this->sound->fresh()->likes()->count();
        $this->isLiked = Auth::check() && Auth::user()->likes()->where('sound_id', $this->sound->id)->exists();
    }

    public function render()
    {
        return view('livewire.like-button');
    }
}