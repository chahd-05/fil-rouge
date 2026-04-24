<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="solar-hero__eyebrow">Admin Dashboard</p>
            <h2 class="text-3xl font-semibold text-white">Platform overview</h2>
        </div>
        <p class="max-w-2xl text-sm leading-7 text-slate-300">
            Follow user activity, engineering output, and the latest saved solar studies from one clear operational view.
        </p>
    </x-slot>

    <div class="solar-stack">
        <section class="solar-hero">
            <p class="solar-hero__eyebrow">Operations Snapshot</p>
            <h1 class="solar-hero__title">A cleaner control surface for the SolarPV platform.</h1>
            <p class="solar-hero__copy">
                This dashboard focuses on visibility: who is using the platform, how many projects are being generated, and which studies need attention first.
            </p>
        </section>

        <section class="solar-card">
            <div class="solar-metrics-grid">
                <div class="solar-stat solar-stat--light">
                    <span class="solar-stat__label">Users</span>
                    <span class="solar-stat__value">{{ $stats['users'] }}</span>
                    <span class="solar-stat__meta">Registered platform users</span>
                </div>
                <div class="solar-stat solar-stat--light">
                    <span class="solar-stat__label">Engineers</span>
                    <span class="solar-stat__value">{{ $stats['engineers'] }}</span>
                    <span class="solar-stat__meta">Technical workspace accounts</span>
                </div>
                <div class="solar-stat solar-stat--light">
                    <span class="solar-stat__label">Admins</span>
                    <span class="solar-stat__value">{{ $stats['admins'] }}</span>
                    <span class="solar-stat__meta">Control panel operators</span>
                </div>
                <div class="solar-stat solar-stat--light">
                    <span class="solar-stat__label">Projects</span>
                    <span class="solar-stat__value">{{ $stats['projects'] }}</span>
                    <span class="solar-stat__meta">Saved solar studies</span>
                </div>
            </div>
        </section>

        <section class="solar-card">
            <h3 class="solar-card__title">Latest projects</h3>
            <p class="solar-card__copy">Recent engineering studies and their current performance profile.</p>

            <div class="solar-list">
                @forelse ($recentProjects as $project)
                    <div class="solar-list-item">
                        <div>
                            <p class="m-0 text-lg font-semibold text-slate-900">{{ $project->city }}</p>
                            <p class="mt-2 text-sm text-slate-500">
                                {{ number_format($project->required_kw, 2) }} kW required, {{ number_format($project->real_kw, 2) }} kW installed, ROI {{ number_format($project->roi_years, 1) }} years
                            </p>
                        </div>
                        <a href="{{ route('projects.show', $project) }}" class="solar-button">Open project</a>
                    </div>
                @empty
                    <div class="solar-empty">No projects available yet.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
