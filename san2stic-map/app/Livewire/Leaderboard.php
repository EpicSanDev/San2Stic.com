<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Leaderboard extends Component
{
    public $users;

    public function mount()
    {
        // Eager load the 'sounds' relationship count
        $this->users = User::withCount('sounds')
            ->orderBy('sounds_count', 'desc')
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.leaderboard')
            ->layout('layouts.app', ['header' => 'Top Contributors']);
    }
}