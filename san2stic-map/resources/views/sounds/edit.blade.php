<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            Edit Sound: {{ $sound->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="card-title mb-4">Audio Waveform</div>
                    
                    <!-- The data-url attribute will be used by app.js to load the sound -->
                    <div id="waveform" class="mb-4" data-url="{{ Storage::disk('s3')->url($sound->path) }}"></div>
                    
                    <div class="card-actions justify-start space-x-2">
                        <button id="playBtn" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Play / Pause
                        </button>
                        <button id="trimBtn" class="btn btn-secondary" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879a1 1 0 01-1.414 0l-4.243-4.243a1 1 0 010-1.414l2.879-2.879m7 7l-7-7" /></svg>
                            Trim Selection
                        </button>
                    </div>
                    <div id="status" class="mt-4 text-sm opacity-75"></div>
                </div>
            </div>
            <livewire:comments-section :sound="$sound" :key="'comments-'.$sound->id" />
        </div>
    </div>
</x-app-layout>
