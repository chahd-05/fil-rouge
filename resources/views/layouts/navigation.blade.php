<nav x-data="{ open: false }" class="solar-nav">
    <div class="solar-nav__inner">
        <a href="{{ route('dashboard') }}" class="solar-brand">
            <x-application-logo class="solar-brand__mark" />
            <span>
                <span class="solar-brand__eyebrow">Smart Energy System</span>
                <span class="solar-brand__name">SolarPV</span>
            </span>
        </a>

        <div class="solar-nav__links">
            <a
                href="{{ route('dashboard') }}"
                class="solar-nav__link {{ request()->routeIs('dashboard') || request()->routeIs(Auth::user()->role . '.dashboard') ? 'is-active' : '' }}"
            >
                Dashboard
            </a>

            @if (in_array(Auth::user()->role, ['engineer', 'admin'], true))
                <a
                    href="{{ route('projects.history') }}"
                    class="solar-nav__link {{ request()->routeIs('projects.*') ? 'is-active' : '' }}"
                >
                    Projects
                </a>
            @endif

            @if (Auth::user()->role === 'engineer')
                <span class="solar-nav__link">Engineering Studio</span>
            @elseif (Auth::user()->role === 'admin')
                <span class="solar-nav__link">Platform Control</span>
            @else
                <span class="solar-nav__link">Solar Estimator</span>
            @endif
        </div>

        <div class="solar-nav__actions">
            <div class="solar-nav__user">
                <span class="solar-nav__user-name">{{ Auth::user()->name }}</span>
                <span class="solar-nav__user-email">{{ Auth::user()->email }}</span>
            </div>

            <a href="{{ route('profile.edit') }}" class="solar-button-secondary">Profile</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="solar-button-ghost">Log Out</button>
            </form>

            <button @click="open = ! open" class="solar-mobile-toggle" type="button" aria-label="Toggle navigation">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div x-cloak x-show="open" x-transition class="solar-nav__mobile">
        <div class="solar-nav__mobile-inner">
            <div class="solar-nav__stack">
                <a href="{{ route('dashboard') }}" class="solar-nav__link {{ request()->routeIs('dashboard') || request()->routeIs(Auth::user()->role . '.dashboard') ? 'is-active' : '' }}">
                    Dashboard
                </a>

                @if (in_array(Auth::user()->role, ['engineer', 'admin'], true))
                    <a href="{{ route('projects.history') }}" class="solar-nav__link {{ request()->routeIs('projects.*') ? 'is-active' : '' }}">
                        Projects
                    </a>
                @endif

                <a href="{{ route('profile.edit') }}" class="solar-button-secondary">Profile</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="solar-button-ghost">Log Out</button>
                </form>
            </div>
        </div>
    </div>
</nav>
