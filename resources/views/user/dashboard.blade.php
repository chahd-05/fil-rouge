<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="solar-hero__eyebrow">User Dashboard</p>
            <h2 class="text-3xl font-semibold text-white">Solar estimate workspace</h2>
        </div>
        <p class="max-w-2xl text-sm leading-7 text-slate-300">
            Review your recent electricity usage, estimate a fitting photovoltaic system, and see savings in a cleaner professional dashboard.
        </p>
    </x-slot>

    <div class="solar-stack">
        <section class="solar-hero">
            <p class="solar-hero__eyebrow">Residential Solar Planning</p>
            <h1 class="solar-hero__title">Turn consumption data into a clear solar recommendation.</h1>
            <p class="solar-hero__copy">
                This interface is built around the real flow of a solar project: usage review, sizing guidance, budget check, and expected savings.
            </p>

            <div class="solar-pill-row">
                <div class="solar-pill">
                    <span class="solar-pill__label">Analysis</span>
                    <span class="solar-pill__value">3-Month Average</span>
                </div>
                <div class="solar-pill">
                    <span class="solar-pill__label">Focus</span>
                    <span class="solar-pill__value">Budget + ROI Clarity</span>
                </div>
                <div class="solar-pill">
                    <span class="solar-pill__label">Outcome</span>
                    <span class="solar-pill__value">Panel Sizing Preview</span>
                </div>
            </div>
        </section>

        <div class="solar-grid-2">
            <section class="solar-card solar-card--dark">
                <h3 class="solar-card__title">Consumption Inputs</h3>
                <p class="solar-card__copy">
                    Enter the last three monthly bills and a few project assumptions to generate a simple solar sizing estimate.
                </p>

                <form action="{{ route('user.calculate') }}" method="POST" class="solar-form">
                    @csrf

                    <div class="solar-form__grid">
                        <div class="solar-field">
                            <label for="m1">Month 1 (kWh)</label>
                            <input id="m1" class="solar-input" type="number" name="m1" value="{{ old('m1', $m1 ?? '') }}" required>
                        </div>
                        <div class="solar-field">
                            <label for="m2">Month 2 (kWh)</label>
                            <input id="m2" class="solar-input" type="number" name="m2" value="{{ old('m2', $m2 ?? '') }}" required>
                        </div>
                    </div>

                    <div class="solar-form__grid">
                        <div class="solar-field">
                            <label for="m3">Month 3 (kWh)</label>
                            <input id="m3" class="solar-input" type="number" name="m3" value="{{ old('m3', $m3 ?? '') }}" required>
                        </div>
                        <div class="solar-field">
                            <label for="price_kwh">Price per kWh (MAD)</label>
                            <input id="price_kwh" class="solar-input" type="number" step="0.01" name="price_kwh" value="{{ old('price_kwh', $pricePerKwh ?? 1.5) }}">
                        </div>
                    </div>

                    <div class="solar-form__grid">
                        <div class="solar-field">
                            <label for="region">Solar region</label>
                            <select id="region" class="solar-select" name="region">
                                <option value="1.2" @selected(old('region') == '1.2')>Strong sun (Oujda)</option>
                                <option value="1.0" @selected(old('region', '1.0') == '1.0')>Average irradiation</option>
                                <option value="0.8" @selected(old('region') == '0.8')>Lower irradiation</option>
                            </select>
                        </div>
                        <div class="solar-field">
                            <label for="budget">Budget (MAD)</label>
                            <input id="budget" class="solar-input" type="number" name="budget" value="{{ old('budget', $budget ?? '') }}">
                        </div>
                    </div>

                    <button type="submit" class="solar-button">Calculate Solar Proposal</button>
                </form>
            </section>

            <section class="solar-card">
                <h3 class="solar-card__title">Project Snapshot</h3>
                <p class="solar-card__copy">
                    A quick overview of the kind of recommendation the calculator generates for a small solar power system.
                </p>

                <div class="solar-metrics-grid" style="margin-top: 1.2rem;">
                    <div class="solar-stat solar-stat--light">
                        <span class="solar-stat__label">Panel Type</span>
                        <span class="solar-stat__value">400 W</span>
                        <span class="solar-stat__meta">Baseline sizing model</span>
                    </div>
                    <div class="solar-stat solar-stat--light">
                        <span class="solar-stat__label">Sun Hours</span>
                        <span class="solar-stat__value">5 h</span>
                        <span class="solar-stat__meta">Reference production window</span>
                    </div>
                    <div class="solar-stat solar-stat--light">
                        <span class="solar-stat__label">Budget Check</span>
                        <span class="solar-stat__value">Live</span>
                        <span class="solar-stat__meta">Compared to system cost</span>
                    </div>
                    <div class="solar-stat solar-stat--light">
                        <span class="solar-stat__label">Savings</span>
                        <span class="solar-stat__value">10 Years</span>
                        <span class="solar-stat__meta">Long-term projection</span>
                    </div>
                </div>

                <div class="solar-card solar-card--soft" style="margin-top: 1.2rem; padding: 1.2rem;">
                    <p class="m-0 text-sm font-semibold text-slate-900">Clearer workflow</p>
                    <p class="mt-2 text-sm leading-7 text-slate-600">
                        The screen is now organized around one simple decision path: enter usage, review the recommendation, compare costs before and after solar, then export the report.
                    </p>
                </div>
            </section>
        </div>

        @if(isset($average))
            <section class="solar-card">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h3 class="solar-card__title">Calculated Solar Recommendation</h3>
                        <p class="solar-card__copy">A simple residential estimate based on your last three months of energy consumption.</p>
                    </div>

                    <form action="{{ route('user.report') }}" method="POST">
                        @csrf
                        <input type="hidden" name="m1" value="{{ $m1 }}">
                        <input type="hidden" name="m2" value="{{ $m2 }}">
                        <input type="hidden" name="m3" value="{{ $m3 }}">
                        <input type="hidden" name="price_kwh" value="{{ $pricePerKwh }}">
                        <button type="submit" class="solar-button">Download PDF Report</button>
                    </form>
                </div>

                <div class="solar-metrics-grid" style="margin-top: 1.4rem;">
                    <div class="solar-stat solar-stat--light">
                        <span class="solar-stat__label">Average</span>
                        <span class="solar-stat__value">{{ number_format($average, 1) }} kWh</span>
                        <span class="solar-stat__meta">Monthly average usage</span>
                    </div>
                    <div class="solar-stat solar-stat--light">
                        <span class="solar-stat__label">Panels</span>
                        <span class="solar-stat__value">{{ $panelCount }}</span>
                        <span class="solar-stat__meta">Estimated panel quantity</span>
                    </div>
                    <div class="solar-stat solar-stat--light">
                        <span class="solar-stat__label">Total Cost</span>
                        <span class="solar-stat__value">{{ number_format($totalCost, 0) }} MAD</span>
                        <span class="solar-stat__meta">Approximate system cost</span>
                    </div>
                    <div class="solar-stat solar-stat--light">
                        <span class="solar-stat__label">Savings</span>
                        <span class="solar-stat__value">{{ number_format($savings, 0) }} MAD</span>
                        <span class="solar-stat__meta">Estimated monthly savings</span>
                    </div>
                </div>

                <div class="solar-grid-2" style="margin-top: 1.3rem;">
                    <div class="solar-card solar-card--soft" style="padding: 1.2rem;">
                        <h4 class="m-0 text-base font-semibold text-slate-900">Decision Notes</h4>
                        <div class="solar-list">
                            <div class="solar-list-item">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">Recommendation</div>
                                    <div class="text-sm text-slate-500">{{ $advice }}</div>
                                </div>
                                <span class="text-sm font-semibold text-amber-600">System Fit</span>
                            </div>
                            <div class="solar-list-item">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">Budget Status</div>
                                    <div class="text-sm text-slate-500">{{ $budgetStatus }}</div>
                                </div>
                                <span class="text-sm font-semibold text-sky-700">Financial Check</span>
                            </div>
                            <div class="solar-list-item">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">Current Bill</div>
                                    <div class="text-sm text-slate-500">{{ number_format($currentCost, 0) }} MAD</div>
                                </div>
                                <span class="text-sm font-semibold text-slate-700">Before Solar</span>
                            </div>
                            <div class="solar-list-item">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">Projected Bill</div>
                                    <div class="text-sm text-slate-500">{{ number_format($afterSolarCost, 0) }} MAD</div>
                                </div>
                                <span class="text-sm font-semibold text-emerald-700">After Solar</span>
                            </div>
                        </div>
                    </div>

                    <div class="solar-card solar-card--soft" style="padding: 1.2rem;">
                        <h4 class="m-0 text-base font-semibold text-slate-900">Savings Horizon</h4>
                        <div class="solar-list">
                            <div class="solar-list-item">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">Monthly Savings</div>
                                    <div class="text-sm text-slate-500">Difference between current and post-solar bill</div>
                                </div>
                                <span class="text-sm font-semibold text-emerald-700">{{ number_format($savings, 0) }} MAD</span>
                            </div>
                            <div class="solar-list-item">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">Yearly Savings</div>
                                    <div class="text-sm text-slate-500">12-month estimate</div>
                                </div>
                                <span class="text-sm font-semibold text-slate-900">{{ number_format($yearlySavings, 0) }} MAD</span>
                            </div>
                            <div class="solar-list-item">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">10-Year Savings</div>
                                    <div class="text-sm text-slate-500">Long-range financial impact</div>
                                </div>
                                <span class="text-sm font-semibold text-amber-600">{{ number_format($savings10Years, 0) }} MAD</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section class="solar-card">
            <div class="flex flex-col gap-2 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h3 class="solar-card__title">Visual Performance Summary</h3>
                    <p class="solar-card__copy">Consumption and savings are displayed side by side so the project is easier to read at a glance.</p>
                </div>
                <p class="text-sm text-slate-500">Charts update with your latest calculation.</p>
            </div>

            <div class="solar-grid-2" style="margin-top: 1.3rem;">
                <div class="solar-chart">
                    <canvas id="consumptionChart"></canvas>
                </div>
                <div class="solar-chart">
                    <canvas id="savingsChart"></canvas>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const consumptionCanvas = document.getElementById('consumptionChart');
        const savingsCanvas = document.getElementById('savingsChart');

        if (consumptionCanvas && consumptionCanvas.getContext) {
            const gradient = consumptionCanvas.getContext('2d').createLinearGradient(0, 0, 0, 220);
            gradient.addColorStop(0, '#f59e0b');
            gradient.addColorStop(1, '#fcd34d');

            new Chart(consumptionCanvas, {
                type: 'bar',
                data: {
                    labels: ['Month 1', 'Month 2', 'Month 3'],
                    datasets: [{
                        label: 'Consumption (kWh)',
                        data: [{{ $m1 ?? 0 }}, {{ $m2 ?? 0 }}, {{ $m3 ?? 0 }}],
                        backgroundColor: gradient,
                        borderRadius: 12,
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            labels: {
                                color: '#334155'
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#64748b' },
                            grid: { display: false }
                        },
                        y: {
                            ticks: { color: '#64748b' },
                            grid: { color: 'rgba(148, 163, 184, 0.18)' }
                        }
                    }
                }
            });
        }

        if (savingsCanvas) {
            new Chart(savingsCanvas, {
                type: 'line',
                data: {
                    labels: ['1 Year', '5 Years', '10 Years'],
                    datasets: [{
                        label: 'Savings (MAD)',
                        data: [
                            {{ $yearlySavings ?? 0 }},
                            {{ ($yearlySavings ?? 0) * 5 }},
                            {{ $savings10Years ?? 0 }}
                        ],
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.14)',
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            labels: {
                                color: '#334155'
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#64748b' },
                            grid: { display: false }
                        },
                        y: {
                            ticks: { color: '#64748b' },
                            grid: { color: 'rgba(148, 163, 184, 0.18)' }
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>
