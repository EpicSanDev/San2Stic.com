<div class="bg-white shadow-md rounded-lg p-4 h-96 flex flex-col">
    <!-- Message Display Area -->
    <div class="flex-grow overflow-y-auto mb-4 border rounded-lg p-3">
        @foreach ($messages as $message)
            <div class="mb-2 @if(is_object($message) && property_exists($message, 'user') && $message->user->id == auth()->id()) text-right @endif">
                <p class="text-xs text-gray-500">
                    @if(is_object($message) && property_exists($message, 'user'))
                        {{ $message->user->name }}
                    @elseif(is_object($message) && property_exists($message, 'user') && is_array($message->user))
                        {{ $message->user['name'] }}
                    @endif
                </p>
                <div class="inline-block p-2 rounded-lg @if(is_object($message) && property_exists($message, 'user') && $message->user->id == auth()->id()) bg-blue-500 text-white @else bg-gray-200 @endif">
                    {{ $message->message }}
                </div>
                <p class="text-xs text-gray-400">
                     {{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}
                </p>
            </div>
        @endforeach
    </div>

    <!-- Message Input Form -->
    <form wire:submit.prevent="sendMessage" class="flex">
        <input wire:model="newMessage" type="text" class="flex-grow border rounded-l-lg p-2" placeholder="Type your message...">
        <button type="submit" class="bg-blue-500 text-white p-2 rounded-r-lg hover:bg-blue-600">Send</button>
    </form>
</div>