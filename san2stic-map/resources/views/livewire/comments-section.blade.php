<div class="mt-8">
    <h3 class="text-xl font-bold mb-4">Comments ({{ $comments->count() }})</h3>

    <!-- Comment List -->
    <div class="space-y-4 mb-6">
        @forelse ($comments as $comment)
            <div class="card bg-base-200 shadow-sm">
                <div class="card-body p-4">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="avatar">
                            <div class="w-8 rounded-full">
                                <img src="{{ $comment->user->profile_photo_url }}" alt="{{ $comment->user->name }}" />
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('users.show', $comment->user) }}" class="font-bold hover:underline">{{ $comment->user->name }}</a>
                            <span class="text-xs opacity-50 ms-2">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <p class="text-base-content">{{ $comment->body }}</p>
                </div>
            </div>
        @empty
            <div class="text-center opacity-75">
                No comments yet. Be the first to comment!
            </div>
        @endforelse
    </div>

    <!-- Add Comment Form -->
    @auth
        <form wire:submit.prevent="addComment" class="flex flex-col space-y-4">
            <textarea wire:model="newComment" class="textarea textarea-bordered w-full" placeholder="Add a comment..."></textarea>
            @error('newComment') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary">
                    <span wire:loading.remove wire:target="addComment">Post Comment</span>
                    <span wire:loading wire:target="addComment" class="loading loading-spinner"></span>
                </button>
            </div>
        </form>
    @else
        <div class="text-center opacity-75">
            <p>Please <a href="{{ route('login') }}" class="link link-primary">log in</a> to leave a comment.</p>
        </div>
    @endauth
</div>