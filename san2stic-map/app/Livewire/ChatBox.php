<?php

namespace App\Livewire;

use App\Events\NewMessage;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\On;

class ChatBox extends Component
{
    public $messages;
    public $newMessage = '';

    public function mount()
    {
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = ChatMessage::with('user')->latest()->take(50)->get()->reverse();
    }

    public function sendMessage()
    {
        if (trim($this->newMessage) === '') {
            return;
        }

        $message = ChatMessage::create([
            'user_id' => Auth::id(),
            'message' => $this->newMessage,
        ]);

        // Load the user relationship
        $message->load('user');

        broadcast(new NewMessage($message))->toOthers();

        $this->messages->push($message);

        $this->newMessage = '';
    }

    #[On('echo:chat,new-message')]
    public function onNewMessage($messageData)
    {
        // Re-create a ChatMessage model instance from the broadcasted data
        // This ensures all relationships and accessors work as expected in the view
        $chatMessage = new ChatMessage((array) $messageData);
        $chatMessage->id = $messageData['id']; // Manually set ID
        $chatMessage->created_at = $messageData['created_at']; // Manually set created_at

        // Manually set the user relationship as it's not a full Eloquent model
        $user = new User((array) $messageData['user']);
        $user->id = $messageData['user']['id'];
        $chatMessage->setRelation('user', $user);

        $this->messages->push($chatMessage);
    }

    public function render()
    {
        return view('livewire.chat-box');
    }
}