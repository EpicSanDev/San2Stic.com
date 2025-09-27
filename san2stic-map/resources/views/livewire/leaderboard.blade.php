<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title text-2xl mb-4">Top 10 Contributors</h2>
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <!-- head -->
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>User</th>
                                    <th>Sounds Uploaded</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $index => $user)
                                    <tr>
                                        <th>{{ $index + 1 }}</th>
                                        <td>
                                            <a href="{{ route('users.show', $user) }}" class="flex items-center space-x-3 hover:opacity-80">
                                                <div class="avatar">
                                                    <div class="mask mask-squircle w-12 h-12">
                                                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold">{{ $user->name }}</div>
                                                    <div class="text-sm opacity-50">Joined {{ $user->created_at->diffForHumans() }}</div>
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary badge-lg">{{ $user->sounds_count }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>