<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="solar-hero__eyebrow">Projects</p>
            <h2 class="text-3xl font-semibold text-white">Saved solar studies</h2>
        </div>
        <p class="max-w-2xl text-sm leading-7 text-slate-300">
            Search by city, filter the project archive, and open any study to inspect the technical and financial result in detail.
        </p>
    </x-slot>

    <div class="solar-stack">
        <section class="solar-card solar-card--dark">
            <h3 class="solar-card__title">Search and filter</h3>
            <p class="solar-card__copy">Keep the archive easy to scan with fast search, city filtering, and pagination controls.</p>

            <form method="GET" action="{{ route('projects.history') }}" class="solar-search" style="margin-top: 1.2rem;">
                <div class="solar-form__grid">
                    <div class="solar-field">
                        <label for="search">Search</label>
                        <input id="search" class="solar-input" type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by city">
                    </div>
                    <div class="solar-field">
                        <label for="city">City filter</label>
                        <select id="city" class="solar-select" name="city">
                            <option value="">All cities</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city }}" @selected(($filters['city'] ?? '') === $city)>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="solar-form__grid">
                    <div class="solar-field">
                        <label for="per_page">Per page</label>
                        <select id="per_page" class="solar-select" name="per_page">
                            @foreach ([10, 15, 25, 50] as $size)
                                <option value="{{ $size }}" @selected((int) ($filters['per_page'] ?? 10) === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="solar-search__actions">
                        <button type="submit" class="solar-button">Apply Filters</button>
                        <a href="{{ route('projects.history') }}" class="solar-button-ghost">Reset</a>
                    </div>
                </div>
            </form>
        </section>

        <section class="solar-card">
            <h3 class="solar-card__title">Project archive</h3>
            <p class="solar-card__copy">Each card gives a quick read on power, cost, and return on investment.</p>

            <div class="solar-list">
                @forelse ($projects as $project)
                    <div class="solar-list-item">
                        <div>
                            <p class="m-0 text-xs font-semibold uppercase tracking-[0.18em] text-amber-600">
                                {{ $project->created_at->format('Y-m-d H:i') }}
                            </p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900">{{ $project->city }}</h3>
                            <p class="mt-2 text-sm text-slate-500">
                                {{ number_format($project->required_kw, 2) }} kW required, {{ number_format($project->real_kw, 2) }} kW real, {{ $project->panels }} panels
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <span class="rounded-full bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
                                ROI {{ number_format($project->roi_years, 1) }} years
                            </span>
                            <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                                {{ number_format(data_get($project->costs, 'total', 0), 2) }} MAD
                            </span>
                            <a href="{{ route('projects.show', $project) }}" class="solar-button">View details</a>
                        </div>
                    </div>
                @empty
                    <div class="solar-empty">No projects found for the current search and filter combination.</div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $projects->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
