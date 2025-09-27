<div class="h-96 flex flex-col">
    <!-- Message Display Area -->
    <div class="flex-grow overflow-y-auto mb-4 p-3 bg-base-200 rounded-lg">
        @forelse ($messages as $message)
            @php
                $isCurrentUser = $message->user->id == auth()->id();
                $userName = $message->user->name;
                $userId = $message->user->id;
            @endphp
            <div class="chat {{ $isCurrentUser ? 'chat-end' : 'chat-start' }}">
                <div class="chat-header">
                    @if($userId)
                        <a href="{{ route('users.show', $userId) }}" class="hover:underline">{{ $userName }}</a>
                    @else
                        {{ $userName }}
                    @endif
                    <time class="text-xs opacity-50">{{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}</time>
                </div>
                <div class="chat-bubble {{ $isCurrentUser ? 'chat-bubble-primary' : '' }}">
                    {{ $message->message }}
                </div>
            </div>
        @empty
            <div class="text-center text-base-content opacity-50">
                No messages yet. Start the conversation!
            </div>
        @endforelse
    </div>

    <!-- Message Input Form -->
    <form wire:submit.prevent="sendMessage" class="flex">
        <input wire:model="newMessage" type="text" class="input input-bordered w-full" placeholder="Type your message...">
        <button type="submit" class="btn btn-primary ms-2">Send</button>
    </form>
</div>
