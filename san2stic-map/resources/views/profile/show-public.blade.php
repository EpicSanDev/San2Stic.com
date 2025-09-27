<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <div class="avatar">
                <div class="w-16 rounded-full">
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
                </div>
            </div>
            <div>
                <h2 class="font-semibold text-2xl text-base-content leading-tight">
                    {{ $user->name }}
                </h2>
                <p class="text-sm opacity-75">Member since {{ $user->created_at->format('M Y') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h3 class="text-xl font-bold mb-4">Sounds uploaded by {{ $user->name }} ({{ $user->sounds->count() }})</h3>

            @if($user->sounds->isEmpty())
                <div class="text-center opacity-75">
                    <p>{{ $user->name }} has not uploaded any sounds yet.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($user->sounds as $sound)
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h2 class="card-title">{{ $sound->name }}</h2>
                                <p>Uploaded {{ $sound->created_at->diffForHumans() }}</p>
                                <div class="card-actions justify-end">
                                    <livewire:like-button :sound="$sound" :key="'like-'.$sound->id" />
                                    <a href="{{ route('sound.edit', $sound) }}" class="btn btn-secondary btn-sm">Edit / Listen</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
