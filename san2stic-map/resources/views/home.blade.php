<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            Welcome to San2Stic Map!
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl mb-8">
                <div class="card-body">
                    <h2 class="card-title text-3xl">Discover Sounds Around the World</h2>
                    <p class="text-lg">San2Stic Map is a community-driven platform where you can share and explore sounds from different locations. Upload your own audio snippets, discover what others are hearing, and connect through the power of sound!</p>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Explore the Map</a>
                        <a href="{{ route('upload') }}" class="btn btn-secondary">Upload Your Sound</a>
                    </div>
                </div>
            </div>

            <h3 class="text-2xl font-bold mb-4">Our Community in Numbers</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-figure text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="stat-title">Total Users</div>
                        <div class="stat-value">{{ $stats['totalUsers'] }}</div>
                    </div>
                </div>
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-figure text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="stat-title">Total Sounds</div>
                        <div class="stat-value">{{ $stats['totalSounds'] }}</div>
                    </div>
                </div>
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-figure text-accent">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="stat-title">Total Likes</div>
                        <div class="stat-value">{{ $stats['totalLikes'] }}</div>
                    </div>
                </div>
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-figure text-info">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="stat-title">Total Comments</div>
                        <div class="stat-value">{{ $stats['totalComments'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
