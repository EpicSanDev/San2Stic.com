<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Sound: ') }} {{ $sound->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- The data-url attribute will be used by app.js to load the sound -->
                <div id="waveform" class="mb-4" data-url="{{ Storage::disk('s3')->url($sound->path) }}"></div>
                
                <div class="flex space-x-2">
                    <button id="playBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Play / Pause
                    </button>
                    <button id="trimBtn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50" disabled>
                        Trim Selection
                    </button>
                </div>
                <div id="status" class="mt-4 text-sm text-gray-600"></div>
            </div>
        </div>
    </div>
</x-app-layout>