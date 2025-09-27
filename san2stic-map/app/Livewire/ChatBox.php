<?php

namespace App\Livewire;

use App\Events\NewMessage;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
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
    public function onNewMessage($message)
    {
        // The incoming message is an array, so we need to convert it to a ChatMessage model instance
        // or just use it as an array/object. For simplicity, we'll add it as is.
        // To make it a real model, we would do:
        // $chatMessage = new ChatMessage($message);
        // $chatMessage->id = $message['id']; // Manually set id as it's not mass-assignable
        // $this->messages->push($chatMessage);
        
        // For simplicity, we'll just add the array to the collection.
        // The view will need to handle both object and array access (e.g., $message->user->name vs $message['user']['name'])
        // A better approach is to re-fetch the message from the DB, but this is more real-time.
        $this->messages->push((object)$message);
    }

    public function render()
    {
        return view('livewire.chat-box');
    }
}