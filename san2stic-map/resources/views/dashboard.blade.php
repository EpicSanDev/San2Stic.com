<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Map Card -->
                <div class="lg:col-span-2">
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title">San2Stic Map</h2>
                            <div id="map" style="height: 500px;" class="rounded-lg"></div>
                        </div>
                    </div>
                </div>

                <!-- Chat Card -->
                <div class="lg:col-span-1">
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title">Live Chat</h2>
                            <livewire:chat-box />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>