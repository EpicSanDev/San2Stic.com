<?php

namespace App\Livewire;

use App\Models\Sound;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CommentsSection extends Component
{
    public Sound $sound;
    public $comments;
    public $newComment = '';

    protected $rules = [
        'newComment' => 'required|string|min:3|max:500',
    ];

    public function mount(Sound $sound)
    {
        $this->sound = $sound;
        $this->loadComments();
    }

    public function loadComments()
    {
        $this->comments = $this->sound->comments()->with('user')->get();
    }

    public function addComment()
    {
        if (Auth::guest()) {
            return redirect()->route('login');
        }

        $this->validate();

        $comment = $this->sound->comments()->create([
            'user_id' => Auth::id(),
            'body' => $this->newComment,
        ]);

        // Eager load the user for the newly created comment
        $comment->load('user');

        $this->comments->prepend($comment); // Add to the beginning of the collection
        $this->newComment = '';
    }

    public function render()
    {
        return view('livewire.comments-section');
    }
}