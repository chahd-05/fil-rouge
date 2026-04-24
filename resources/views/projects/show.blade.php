<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="solar-hero__eyebrow">Project Detail</p>
            <h2 class="text-3xl font-semibold text-white">{{ data_get($dashboardPayload, 'result.input.project_name', $project->name) }}</h2>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('projects.history') }}" class="solar-button-ghost">Back</a>
            <a href="{{ route('engineer.dashboard', ['project' => $project->id]) }}" class="solar-button-ghost">Edit in engineer studio</a>
            <a href="{{ route('projects.report', $project) }}" class="solar-button">Download PDF</a>
        </div>
    </x-slot>

    @php($result = $dashboardPayload['result'])

    <div class="solar-stack">
        <section class="solar-card">
            <div class="solar-metrics-grid">
                <div class="solar-stat solar-stat--light">
                    <span class="solar-stat__label">Required PV</span>
                    <span class="solar-stat__value">{{ number_format(data_get($result, 'solar.required_kw', 0), 2) }} kW</span>
                    <span class="solar-stat__meta">{{ data_get($result, 'input.city') }}, {{ data_get($result, 'input.country') }}</span>
                </div>
                <div class="solar-stat solar-stat--light">
                    <span class="solar-stat__label">Installed PV</span>
                    <span class="solar-stat__value">{{ number_format(data_get($result, 'solar.real_kw', 0), 2) }} kW</span>
                    <span class="solar-stat__meta">{{ data_get($result, 'solar.panels', 0) }} modules</span>
                </div>
                <div class="solar-stat solar-stat--light">
                    <span class="solar-stat__label">Total investment</span>
                    <span class="solar-stat__value">{{ number_format(data_get($result, 'costs.total', 0), 2) }} MAD</span>
                    <span class="solar-stat__meta">ROI {{ number_format(data_get($result, 'costs.roi_years', 0), 1) }} years</span>
                </div>
                <div class="solar-stat solar-stat--light">
                    <span class="solar-stat__label">Global status</span>
                    <span class="solar-stat__value">{{ ucfirst(data_get($result, 'global.status', 'n/a')) }}</span>
                    <span class="solar-stat__meta">Engineering validation</span>
                </div>
            </div>
        </section>

        <div class="solar-grid-2">
            <section class="solar-card">
                <h3 class="solar-card__title">Input data</h3>
                <div class="solar-list">
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Installation type</span><span class="text-sm font-semibold text-slate-900">{{ data_get($result, 'input.installation_type') }}</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Daily consumption</span><span class="text-sm font-semibold text-slate-900">{{ number_format(data_get($result, 'input.daily_consumption', 0), 2) }} kWh/day</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Peak power</span><span class="text-sm font-semibold text-slate-900">{{ number_format(data_get($result, 'input.peak_power', 0), 2) }} kW</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Irradiation</span><span class="text-sm font-semibold text-slate-900">{{ number_format(data_get($result, 'irradiation', 0), 0) }} kWh/m²/year</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Tilt / Azimuth</span><span class="text-sm font-semibold text-slate-900">{{ number_format(data_get($result, 'input.tilt_angle', 0), 1) }}° / {{ number_format(data_get($result, 'input.azimuth', 0), 1) }}°</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Available surface</span><span class="text-sm font-semibold text-slate-900">{{ number_format(data_get($result, 'input.available_surface', 0), 1) }} m²</span></div>
                </div>
            </section>

            <section class="solar-card">
                <h3 class="solar-card__title">System design</h3>
                <div class="solar-list">
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Panel model</span><span class="text-sm font-semibold text-slate-900">{{ data_get($result, 'panel.model') }}</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Inverter</span><span class="text-sm font-semibold text-slate-900">{{ data_get($result, 'inverter.count') }} x {{ number_format(data_get($result, 'inverter.value', 0), 2) }} kW</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Stringing</span><span class="text-sm font-semibold text-slate-900">{{ data_get($result, 'stringing.strings') }} strings / {{ data_get($result, 'stringing.panels_per_string') }} panels</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">DC cable</span><span class="text-sm font-semibold text-slate-900">{{ number_format(data_get($result, 'cable.dc.standard', 0), 2) }} mm²</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">AC cable</span><span class="text-sm font-semibold text-slate-900">{{ number_format(data_get($result, 'cable.ac.standard', 0), 2) }} mm²</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Protections</span><span class="text-sm font-semibold text-slate-900">Breaker {{ data_get($result, 'protection.breaker') }} A / Fuse {{ data_get($result, 'protection.fuse') }} A</span></div>
                </div>
            </section>
        </div>

        <div class="solar-grid-2">
            <section class="solar-card">
                <h3 class="solar-card__title">Financial summary</h3>
                <div class="solar-list">
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Material total</span><span class="text-sm font-semibold text-slate-900">{{ number_format(data_get($result, 'costs.material_total', 0), 2) }} MAD</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Installation</span><span class="text-sm font-semibold text-slate-900">{{ number_format(data_get($result, 'costs.installation', 0), 2) }} MAD</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Annual savings</span><span class="text-sm font-semibold text-emerald-700">{{ number_format(data_get($result, 'costs.annual_savings', 0), 2) }} MAD</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">10-year profit</span><span class="text-sm font-semibold text-slate-900">{{ number_format(data_get($result, 'costs.ten_year_profit', 0), 2) }} MAD</span></div>
                    <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Budget status</span><span class="text-sm font-semibold text-slate-900">{{ ucwords(str_replace('-', ' ', data_get($result, 'costs.budget_status', 'n/a'))) }}</span></div>
                </div>
            </section>

            <section class="solar-card">
                <h3 class="solar-card__title">Recommendations</h3>
                <div class="solar-list">
                    @forelse (data_get($result, 'suggestions', []) as $suggestion)
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-7 text-amber-900">
                            {{ $suggestion }}
                        </div>
                    @empty
                        <div class="solar-empty">No additional recommendation saved for this project.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
