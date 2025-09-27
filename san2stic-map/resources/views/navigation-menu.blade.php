<div class="navbar bg-base-100 shadow-lg">
    <div class="navbar-start">
        <!-- Hamburger for mobile -->
        <div class="dropdown">
            <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
            </div>
            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('upload') }}" class="{{ request()->routeIs('upload') ? 'active' : '' }}">Upload Sound</a></li>
            <li><a href="{{ route('leaderboard') }}" class="{{ request()->routeIs('leaderboard') ? 'active' : '' }}">Leaderboard</a></li>
            </ul>
        </div>
        <!-- Logo -->
        <a href="{{ route('dashboard') }}" class="btn btn-ghost text-xl">
            <x-application-mark class="block h-9 w-auto" />
            San2Stic Map
        </a>
    </div>

    <!-- Main Menu for Desktop -->
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1">
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
            <li><a href="{{ route('upload') }}" class="{{ request()->routeIs('upload') ? 'active' : '' }}">Upload Sound</a></li>
            <li><a href="{{ route('leaderboard') }}" class="{{ request()->routeIs('leaderboard') ? 'active' : '' }}">Leaderboard</a></li>
        </ul>
    </div>

    <div class="navbar-end">
        <!-- Teams Dropdown -->
        @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost">
                    {{ Auth::user()->currentTeam->name }}
                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                </div>
                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-60">
                    <li class="menu-title"><span>Manage Team</span></li>
                    <li><a href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">Team Settings</a></li>
                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <li><a href="{{ route('teams.create') }}">Create New Team</a></li>
                    @endcan

                    @if (Auth::user()->allTeams()->count() > 1)
                        <div class="divider my-1"></div>
                        <li class="menu-title"><span>Switch Teams</span></li>
                        @foreach (Auth::user()->allTeams() as $team)
                            <form method="POST" action="{{ route('current-team.update') }}" x-data>
                                @method('PUT')
                                @csrf
                                <input type="hidden" name="team_id" value="{{ $team->id }}">
                                <li><a href="#" @click.prevent="$root.submit();">{{ $team->name }}</a></li>
                            </form>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endif

        <!-- User Profile Dropdown -->
        <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                <div class="w-10 rounded-full">
                    <img alt="User Profile" src="{{ Auth::user()->profile_photo_url }}" />
                </div>
            </div>
            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                <li class="menu-title"><span>Manage Account</span></li>
                <li><a href="{{ route('profile.show') }}">Profile</a></li>
                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <li><a href="{{ route('api-tokens.index') }}">API Tokens</a></li>
                @endif
                <div class="divider my-1"></div>
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <li><a href="{{ route('logout') }}" @click.prevent="$root.submit();">Log Out</a></li>
                </form>
            </ul>
        </div>
    </div>
</div>