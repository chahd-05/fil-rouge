<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="solar-hero__eyebrow">Engineer Workspace</p>
            <h2 class="text-3xl font-semibold text-white">Photovoltaic installation calculation studio</h2>
        </div>
        <p class="max-w-3xl text-sm leading-7 text-slate-300">
            Capture base project data, run full photovoltaic engineering calculations, review technical checks and financial results, then save or update the project from one dashboard.
        </p>
    </x-slot>

    <div class="solar-stack">
        @if (session('status'))
            <div class="solar-flash">{{ session('status') }}</div>
        @endif

        <section class="solar-hero">
            <p class="solar-hero__eyebrow">End-to-End Solar Engineering</p>
            <h1 class="solar-hero__title">From site data to system design, protections, costs, charts, and report-ready output.</h1>
            <p class="solar-hero__copy">
                The dashboard below is organized the way an engineering study is usually prepared: project information, electrical demand, solar resource, installation constraints, environmental assumptions, automated equipment selection, and full validation.
            </p>

            <div class="solar-pill-row">
                <div class="solar-pill">
                    <span class="solar-pill__label">Scope</span>
                    <span class="solar-pill__value">PV sizing + stringing</span>
                </div>
                <div class="solar-pill">
                    <span class="solar-pill__label">Outputs</span>
                    <span class="solar-pill__value">Charts + PDF report</span>
                </div>
                <div class="solar-pill">
                    <span class="solar-pill__label">Checks</span>
                    <span class="solar-pill__value">Surface, MPPT, cable, protection</span>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[430px_minmax(0,1fr)]">
            <section class="solar-card solar-card--dark">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="solar-card__title">Project Input Form</h3>
                        <p class="solar-card__copy">Structured project data for a transparent photovoltaic engineering calculation.</p>
                    </div>
                    @if ($project)
                        <span class="rounded-full border border-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-sky-200">
                            Editing #{{ $project->id }}
                        </span>
                    @endif
                </div>

                <form id="engineer-form" method="POST" action="{{ route('engineer.calculate') }}" class="solar-form">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ old('project_id', $formData['project_id']) }}">

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-300">General Info</p>
                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="project_name">Project name</label>
                                <input id="project_name" class="solar-input" type="text" name="project_name" value="{{ old('project_name', $formData['project_name']) }}">
                                @error('project_name') <p class="text-sm text-rose-300">{{ $message }}</p> @enderror
                            </div>
                            <div class="solar-field">
                                <label for="installation_type">Installation type</label>
                                <select id="installation_type" class="solar-select" name="installation_type">
                                    <option value="Residential" @selected(old('installation_type', $formData['installation_type']) === 'Residential')>Residential</option>
                                    <option value="Industrial" @selected(old('installation_type', $formData['installation_type']) === 'Industrial')>Industrial</option>
                                </select>
                            </div>
                        </div>

                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="city">City</label>
                                <input id="city" class="solar-input" type="text" name="city" value="{{ old('city', $formData['city']) }}">
                                @error('city') <p class="text-sm text-rose-300">{{ $message }}</p> @enderror
                            </div>
                            <div class="solar-field">
                                <label for="country">Country</label>
                                <input id="country" class="solar-input" type="text" name="country" value="{{ old('country', $formData['country']) }}">
                                @error('country') <p class="text-sm text-rose-300">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="latitude">Latitude</label>
                                <input id="latitude" class="solar-input" type="number" step="0.0001" name="latitude" value="{{ old('latitude', $formData['latitude']) }}">
                            </div>
                            <div class="solar-field">
                                <label for="longitude">Longitude</label>
                                <input id="longitude" class="solar-input" type="number" step="0.0001" name="longitude" value="{{ old('longitude', $formData['longitude']) }}">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-300">Electrical Needs</p>
                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="daily_consumption">Daily consumption (kWh/day)</label>
                                <input id="daily_consumption" class="solar-input" type="number" step="0.1" name="daily_consumption" value="{{ old('daily_consumption', $formData['daily_consumption']) }}">
                                @error('daily_consumption') <p class="text-sm text-rose-300">{{ $message }}</p> @enderror
                            </div>
                            <div class="solar-field">
                                <label for="peak_power">Peak power (kW)</label>
                                <input id="peak_power" class="solar-input" type="number" step="0.1" name="peak_power" value="{{ old('peak_power', $formData['peak_power']) }}">
                            </div>
                        </div>

                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="day_usage_percent">Day usage (%)</label>
                                <input id="day_usage_percent" class="solar-input" type="number" step="0.1" name="day_usage_percent" value="{{ old('day_usage_percent', $formData['day_usage_percent']) }}">
                            </div>
                            <div class="solar-field">
                                <label for="night_usage_percent">Night usage (%)</label>
                                <input id="night_usage_percent" class="solar-input" type="number" step="0.1" name="night_usage_percent" value="{{ old('night_usage_percent', $formData['night_usage_percent']) }}">
                                @error('night_usage_percent') <p class="text-sm text-rose-300">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="autonomy_days">Autonomy days</label>
                                <input id="autonomy_days" class="solar-input" type="number" step="0.1" name="autonomy_days" value="{{ old('autonomy_days', $formData['autonomy_days']) }}">
                            </div>
                            <div class="solar-field">
                                <label for="price_per_kwh">Electricity tariff (MAD/kWh)</label>
                                <input id="price_per_kwh" class="solar-input" type="number" step="0.01" name="price_per_kwh" value="{{ old('price_per_kwh', $formData['price_per_kwh']) }}">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-300">Solar Data</p>
                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="irradiation">Irradiation (kWh/m²/year)</label>
                                <input id="irradiation" class="solar-input" type="number" step="0.1" name="irradiation" value="{{ old('irradiation', $formData['irradiation']) }}">
                            </div>
                            <div class="solar-field">
                                <label for="use_auto_irradiation">Resource source</label>
                                <select id="use_auto_irradiation" class="solar-select" name="use_auto_irradiation">
                                    <option value="1" @selected((int) old('use_auto_irradiation', $formData['use_auto_irradiation']) === 1)>Auto fetch from API</option>
                                    <option value="0" @selected((int) old('use_auto_irradiation', $formData['use_auto_irradiation']) === 0)>Use manual irradiation</option>
                                </select>
                            </div>
                        </div>

                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="tilt_angle">Tilt angle (deg)</label>
                                <input id="tilt_angle" class="solar-input" type="number" step="0.1" name="tilt_angle" value="{{ old('tilt_angle', $formData['tilt_angle']) }}">
                            </div>
                            <div class="solar-field">
                                <label for="azimuth">Orientation azimuth (deg)</label>
                                <input id="azimuth" class="solar-input" type="number" step="0.1" name="azimuth" value="{{ old('azimuth', $formData['azimuth']) }}">
                            </div>
                        </div>

                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="performance_ratio">Performance ratio</label>
                                <input id="performance_ratio" class="solar-input" type="number" step="0.01" name="performance_ratio" value="{{ old('performance_ratio', $formData['performance_ratio']) }}">
                            </div>
                            <div class="solar-field">
                                <label for="degradation">Annual degradation (%)</label>
                                <input id="degradation" class="solar-input" type="number" step="0.1" name="degradation" value="{{ old('degradation', $formData['degradation']) }}">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-300">Installation Constraints</p>
                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="available_surface">Available surface (m²)</label>
                                <input id="available_surface" class="solar-input" type="number" step="0.1" name="available_surface" value="{{ old('available_surface', $formData['available_surface']) }}">
                            </div>
                            <div class="solar-field">
                                <label for="mounting_type">Mounting type</label>
                                <select id="mounting_type" class="solar-select" name="mounting_type">
                                    <option value="roof" @selected(old('mounting_type', $formData['mounting_type']) === 'roof')>Roof</option>
                                    <option value="ground" @selected(old('mounting_type', $formData['mounting_type']) === 'ground')>Ground</option>
                                </select>
                            </div>
                        </div>

                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="structure_type">Structure type</label>
                                <select id="structure_type" class="solar-select" name="structure_type">
                                    <option value="fixed" @selected(old('structure_type', $formData['structure_type']) === 'fixed')>Fixed</option>
                                    <option value="tracking" @selected(old('structure_type', $formData['structure_type']) === 'tracking')>Tracking</option>
                                </select>
                            </div>
                            <div class="solar-field">
                                <label for="budget">Budget (MAD)</label>
                                <input id="budget" class="solar-input" type="number" step="0.01" name="budget" value="{{ old('budget', $formData['budget']) }}">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-300">Environment & Losses</p>
                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="temperature_min">Temperature min (°C)</label>
                                <input id="temperature_min" class="solar-input" type="number" step="0.1" name="temperature_min" value="{{ old('temperature_min', $formData['temperature_min']) }}">
                            </div>
                            <div class="solar-field">
                                <label for="temperature_max">Temperature max (°C)</label>
                                <input id="temperature_max" class="solar-input" type="number" step="0.1" name="temperature_max" value="{{ old('temperature_max', $formData['temperature_max']) }}">
                            </div>
                        </div>

                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="cable_length">Cable length (m)</label>
                                <input id="cable_length" class="solar-input" type="number" step="0.1" name="cable_length" value="{{ old('cable_length', $formData['cable_length']) }}">
                            </div>
                            <div class="solar-field">
                                <label for="temperature_loss_percent">Temperature losses (%)</label>
                                <input id="temperature_loss_percent" class="solar-input" type="number" step="0.1" name="temperature_loss_percent" value="{{ old('temperature_loss_percent', $formData['temperature_loss_percent']) }}">
                            </div>
                        </div>

                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="inverter_loss_percent">Inverter losses (%)</label>
                                <input id="inverter_loss_percent" class="solar-input" type="number" step="0.1" name="inverter_loss_percent" value="{{ old('inverter_loss_percent', $formData['inverter_loss_percent']) }}">
                            </div>
                            <div class="solar-field">
                                <label for="dust_loss_percent">Dust losses (%)</label>
                                <input id="dust_loss_percent" class="solar-input" type="number" step="0.1" name="dust_loss_percent" value="{{ old('dust_loss_percent', $formData['dust_loss_percent']) }}">
                            </div>
                        </div>

                        <div class="solar-field mt-4">
                            <label for="other_loss_percent">Other losses (%)</label>
                            <input id="other_loss_percent" class="solar-input" type="number" step="0.1" name="other_loss_percent" value="{{ old('other_loss_percent', $formData['other_loss_percent']) }}">
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-300">Equipment Catalog</p>
                        <div class="solar-form__grid mt-4">
                            <div class="solar-field">
                                <label for="panel_id">Panel selection</label>
                                <select id="panel_id" class="solar-select" name="panel_id">
                                    <option value="">Auto select best fit</option>
                                    @foreach ($panelOptions as $panel)
                                        <option value="{{ $panel->id }}" @selected(old('panel_id', $formData['panel_id']) == $panel->id)>
                                            {{ $panel->model }} - {{ $panel->power }} W
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="solar-field">
                                <label for="inverter_id">Inverter selection</label>
                                <select id="inverter_id" class="solar-select" name="inverter_id">
                                    <option value="">Auto select by DC power</option>
                                    @foreach ($inverterOptions as $inverter)
                                        <option value="{{ $inverter->id }}" @selected(old('inverter_id', $formData['inverter_id']) == $inverter->id)>
                                            {{ $inverter->model }} - {{ $inverter->ac_power_kw ?? $inverter->power }} kW
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="solar-field mt-4">
                            <label for="cable_id">Preferred cable reference</label>
                            <select id="cable_id" class="solar-select" name="cable_id">
                                <option value="">Auto select by current and voltage drop</option>
                                @foreach ($cableOptions as $cable)
                                    <option value="{{ $cable->id }}" @selected(old('cable_id', $formData['cable_id']) == $cable->id)>
                                        {{ $cable->section }} mm² {{ $cable->material }} {{ $cable->voltage_type ? ' - ' . $cable->voltage_type : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button id="submit-button" type="submit" class="solar-button">
                            {{ $project ? 'Update Project' : 'Calculate And Save Project' }}
                        </button>
                        @if ($project)
                            <a href="{{ route('engineer.dashboard') }}" class="solar-button-ghost">New Project</a>
                        @endif
                    </div>
                </form>
            </section>

            <section class="solar-stack">
                <div id="preview-panel" class="solar-card">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="solar-card__title">Live Preview</h3>
                            <p class="solar-card__copy">Quick engineering preview while inputs change.</p>
                        </div>
                        <span id="preview-status" class="text-sm font-semibold text-slate-500">Waiting for valid input</span>
                    </div>

                    <div class="solar-metrics-grid" style="margin-top: 1.2rem;">
                        <div class="solar-stat solar-stat--light">
                            <span class="solar-stat__label">Required PV</span>
                            <span id="preview-required" class="solar-stat__value">-</span>
                            <span class="solar-stat__meta">Sizing target</span>
                        </div>
                        <div class="solar-stat solar-stat--light">
                            <span class="solar-stat__label">Installed PV</span>
                            <span id="preview-real" class="solar-stat__value">-</span>
                            <span class="solar-stat__meta">Selected array</span>
                        </div>
                        <div class="solar-stat solar-stat--light">
                            <span class="solar-stat__label">Investment</span>
                            <span id="preview-cost" class="solar-stat__value">-</span>
                            <span class="solar-stat__meta">Estimated CAPEX</span>
                        </div>
                        <div class="solar-stat solar-stat--light">
                            <span class="solar-stat__label">ROI</span>
                            <span id="preview-roi" class="solar-stat__value">-</span>
                            <span class="solar-stat__meta">Years</span>
                        </div>
                    </div>
                </div>

                @if ($result)
                    <section id="project-results" class="solar-card">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                            <div>
                                <h3 class="solar-card__title">Engineering results</h3>
                                <p class="solar-card__copy">Detailed technical and financial synthesis for {{ $result['input']['project_name'] }}.</p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                @if ($project)
                                    <a href="{{ route('engineer.dashboard', ['project' => $project->id]) }}#project-results" class="solar-button-light">View in workspace</a>
                                    <a href="{{ route('projects.report', $project) }}" class="solar-button">Download PDF</a>
                                @endif
                            </div>
                        </div>

                        <div class="solar-metrics-grid" style="margin-top: 1.3rem;">
                            <div class="solar-stat solar-stat--light">
                                <span class="solar-stat__label">Required PV</span>
                                <span class="solar-stat__value">{{ number_format($result['solar']['required_kw'], 2) }} kW</span>
                                <span class="solar-stat__meta">{{ $result['panel']['model'] }}</span>
                            </div>
                            <div class="solar-stat solar-stat--light">
                                <span class="solar-stat__label">Installed PV</span>
                                <span class="solar-stat__value">{{ number_format($result['solar']['real_kw'], 2) }} kW</span>
                                <span class="solar-stat__meta">{{ $result['solar']['panels'] }} panels</span>
                            </div>
                            <div class="solar-stat solar-stat--light">
                                <span class="solar-stat__label">Annual Production</span>
                                <span class="solar-stat__value">{{ number_format($result['production']['yearly_kwh'], 0) }} kWh</span>
                                <span class="solar-stat__meta">Specific yield {{ number_format($result['production']['specific_yield'], 0) }} kWh/kWp</span>
                            </div>
                            <div class="solar-stat solar-stat--light">
                                <span class="solar-stat__label">ROI</span>
                                <span class="solar-stat__value">{{ number_format($result['costs']['roi_years'], 1) }} years</span>
                                <span class="solar-stat__meta">Payback {{ number_format($result['costs']['payback_years'], 1) }} years</span>
                            </div>
                        </div>
                    </section>

                    <div class="solar-grid-2">
                        <section class="solar-card">
                            <h3 class="solar-card__title">Detailed calculations</h3>
                            <div class="solar-list">
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">PV power formula</div>
                                        <div class="text-sm text-slate-500">{{ $result['formulas']['pv_power'] }}</div>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ number_format($result['solar']['required_kw'], 2) }} kW</span>
                                </div>
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Panel count formula</div>
                                        <div class="text-sm text-slate-500">{{ $result['formulas']['panel_count'] }}</div>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ $result['solar']['panels'] }} modules</span>
                                </div>
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">DC/AC ratio</div>
                                        <div class="text-sm text-slate-500">{{ $result['formulas']['dc_ac_ratio'] }}</div>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ number_format($result['inverter']['dc_ac_ratio'], 2) }}</span>
                                </div>
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Cable sizing formula</div>
                                        <div class="text-sm text-slate-500">{{ $result['formulas']['cable_section'] }}</div>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">DC {{ number_format(data_get($result, 'cable.dc.standard', 0), 2) }} / AC {{ number_format(data_get($result, 'cable.ac.standard', 0), 2) }} mm²</span>
                                </div>
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">ROI formula</div>
                                        <div class="text-sm text-slate-500">{{ $result['formulas']['roi'] }}</div>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ number_format($result['costs']['annual_savings'], 2) }} MAD/year</span>
                                </div>
                            </div>
                        </section>

                        <section class="solar-card">
                            <h3 class="solar-card__title">Validation and smart suggestions</h3>
                            <div class="solar-list">
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Global status</div>
                                        <div class="text-sm text-slate-500">Overall engineering approval state</div>
                                    </div>
                                    <span class="text-sm font-semibold {{ $result['global']['status'] === 'approved' ? 'text-emerald-700' : ($result['global']['status'] === 'review' ? 'text-amber-600' : 'text-rose-600') }}">
                                        {{ ucfirst($result['global']['status']) }}
                                    </span>
                                </div>
                                @foreach ($result['checks'] as $name => $check)
                                    <div class="solar-list-item">
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">{{ ucwords(str_replace('_', ' ', $name)) }}</div>
                                            <div class="text-sm text-slate-500">{{ $check['message'] ?? 'Validation check completed' }}</div>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-900">{{ ucfirst($check['status']) }}</span>
                                    </div>
                                @endforeach
                                @foreach ($result['suggestions'] as $suggestion)
                                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-7 text-amber-900">
                                        {{ $suggestion }}
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    </div>

                    <div class="solar-grid-2">
                        <section class="solar-card">
                            <h3 class="solar-card__title">System design</h3>
                            <div class="solar-grid-2" style="margin-top: 1.2rem;">
                                <div class="solar-stat solar-stat--light">
                                    <span class="solar-stat__label">Panel layout</span>
                                    <span class="solar-stat__value">{{ $result['structure']['rows'] }} rows</span>
                                    <span class="solar-stat__meta">{{ $result['structure']['panels_per_row'] }} panels/row</span>
                                </div>
                                <div class="solar-stat solar-stat--light">
                                    <span class="solar-stat__label">Surface used</span>
                                    <span class="solar-stat__value">{{ number_format($result['solar']['surface_used_m2'], 2) }} m²</span>
                                    <span class="solar-stat__meta">{{ number_format($result['structure']['surface_usage_percent'], 1) }}% of site</span>
                                </div>
                                <div class="solar-stat solar-stat--light">
                                    <span class="solar-stat__label">Inverter</span>
                                    <span class="solar-stat__value">{{ $result['inverter']['count'] }} x {{ number_format($result['inverter']['value'], 2) }} kW</span>
                                    <span class="solar-stat__meta">{{ $result['inverter']['type'] }}</span>
                                </div>
                                <div class="solar-stat solar-stat--light">
                                    <span class="solar-stat__label">Optimized tilt</span>
                                    <span class="solar-stat__value">{{ number_format($result['structure']['optimized_tilt_deg'], 1) }}°</span>
                                    <span class="solar-stat__meta">Current input {{ number_format($result['input']['tilt_angle'], 1) }}°</span>
                                </div>
                            </div>

                            <div class="solar-list">
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">String design</div>
                                        <div class="text-sm text-slate-500">{{ $result['stringing']['strings'] }} strings, {{ $result['stringing']['panels_per_string'] }} modules/string</div>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ $result['stringing']['mppt_used'] }} MPPT used</span>
                                </div>
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Voltage window</div>
                                        <div class="text-sm text-slate-500">Voc {{ number_format($result['stringing']['string_voc'], 1) }} V, Vmp {{ number_format($result['stringing']['string_vmp'], 1) }} V</div>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ $result['stringing']['within_voltage_limits'] ? 'OK' : 'Review' }}</span>
                                </div>
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Current check</div>
                                        <div class="text-sm text-slate-500">String current {{ number_format($result['stringing']['string_current'], 2) }} A</div>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ $result['stringing']['within_current_limits'] ? 'OK' : 'Review' }}</span>
                                </div>
                            </div>
                        </section>

                        <section class="solar-card">
                            <h3 class="solar-card__title">Electrical, structure, and protection</h3>
                            <div class="solar-list">
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">DC cable</div>
                                        <div class="text-sm text-slate-500">Computed {{ number_format(data_get($result, 'cable.dc.section', 0), 2) }} mm², selected {{ number_format(data_get($result, 'cable.dc.standard', 0), 2) }} mm²</div>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ number_format(data_get($result, 'cable.dc.voltage_drop_percent', 0), 2) }}%</span>
                                </div>
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">AC cable</div>
                                        <div class="text-sm text-slate-500">Computed {{ number_format(data_get($result, 'cable.ac.section', 0), 2) }} mm², selected {{ number_format(data_get($result, 'cable.ac.standard', 0), 2) }} mm²</div>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ number_format(data_get($result, 'cable.ac.voltage_drop_percent', 0), 2) }}%</span>
                                </div>
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Protection package</div>
                                        <div class="text-sm text-slate-500">Breaker {{ $result['protection']['breaker'] }} A, fuse {{ $result['protection']['fuse'] }} A, SPD {{ $result['protection']['spd'] }} A</div>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">Earthing {{ $result['protection']['earthing'] }} A</span>
                                </div>
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Wind and weight</div>
                                        <div class="text-sm text-slate-500">Wind {{ number_format($result['structure']['wind_pressure_kN_m2'], 2) }} kN/m²</div>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ number_format($result['structure']['weight_distribution_kg_m2'], 2) }} kg/m²</span>
                                </div>
                                <div class="solar-list-item">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Foundations</div>
                                        <div class="text-sm text-slate-500">{{ $result['massifs']['count'] }} massifs, {{ $result['massifs']['supports_per_row'] }} supports/row</div>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ number_format($result['massifs']['concrete_volume_m3'], 2) }} m³</span>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="solar-grid-2">
                        <section class="solar-card">
                            <h3 class="solar-card__title">Financial analysis</h3>
                            <div class="solar-list">
                                <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Panels</span><span class="text-sm font-semibold text-slate-900">{{ number_format($result['costs']['panel'], 2) }} MAD</span></div>
                                <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Inverters</span><span class="text-sm font-semibold text-slate-900">{{ number_format($result['costs']['inverter'], 2) }} MAD</span></div>
                                <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">DC cables</span><span class="text-sm font-semibold text-slate-900">{{ number_format($result['costs']['dc_cable'], 2) }} MAD</span></div>
                                <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">AC cables</span><span class="text-sm font-semibold text-slate-900">{{ number_format($result['costs']['ac_cable'], 2) }} MAD</span></div>
                                <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Structure + massifs</span><span class="text-sm font-semibold text-slate-900">{{ number_format($result['costs']['structure'] + $result['costs']['massifs'], 2) }} MAD</span></div>
                                <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Protection + installation</span><span class="text-sm font-semibold text-slate-900">{{ number_format($result['costs']['protection'] + $result['costs']['installation'], 2) }} MAD</span></div>
                                <div class="solar-list-item"><span class="text-sm font-semibold text-slate-900">Total investment</span><span class="text-base font-bold text-slate-900">{{ number_format($result['costs']['total'], 2) }} MAD</span></div>
                                <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Annual savings</span><span class="text-sm font-semibold text-emerald-700">{{ number_format($result['costs']['annual_savings'], 2) }} MAD</span></div>
                                <div class="solar-list-item"><span class="text-sm font-semibold text-slate-700">Budget status</span><span class="text-sm font-semibold text-slate-900">{{ ucwords(str_replace('-', ' ', $result['costs']['budget_status'])) }}</span></div>
                            </div>
                        </section>

                        <section class="solar-card">
                            <h3 class="solar-card__title">Scenario comparison</h3>
                            <p class="solar-card__copy">Alternative panel options ranked by ROI and total investment.</p>
                                <div class="solar-list">
                                    @forelse ($comparison as $scenario)
                                        <div class="solar-list-item">
                                            <div>
                                                <div class="text-sm font-semibold text-slate-900">
                                                    {{ data_get($scenario, 'panel_model', 'Panel scenario') }} - {{ data_get($scenario, 'panel_power', 0) }} W
                                                </div>
                                                <div class="text-sm text-slate-500">
                                                    {{ data_get($scenario, 'panels', 0) }} panels,
                                                    {{ number_format((float) data_get($scenario, 'real_kw', 0), 2) }} kW,
                                                    {{ number_format((float) data_get($scenario, 'surface_used_m2', 0), 1) }} m²
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-semibold text-slate-900">{{ number_format((float) data_get($scenario, 'total_cost', 0), 2) }} MAD</div>
                                                <div class="text-sm {{ $recommendedScenario && data_get($recommendedScenario, 'panel_power') === data_get($scenario, 'panel_power') ? 'text-amber-600' : 'text-emerald-700' }}">
                                                    ROI {{ number_format((float) data_get($scenario, 'roi_years', 0), 1) }} y
                                                </div>
                                            </div>
                                        </div>
                                @empty
                                    <div class="solar-empty">Scenario comparison will appear after a successful calculation.</div>
                                @endforelse
                            </div>
                        </section>
                    </div>

                    <section class="solar-card">
                        <h3 class="solar-card__title">Charts</h3>
                        <div class="grid gap-4 lg:grid-cols-2" style="margin-top: 1.2rem;">
                            <div class="solar-chart"><canvas id="energyBalanceChart"></canvas></div>
                            <div class="solar-chart"><canvas id="costBreakdownChart"></canvas></div>
                            <div class="solar-chart"><canvas id="roiChart"></canvas></div>
                            <div class="solar-chart"><canvas id="monthlyProductionChart"></canvas></div>
                        </div>
                    </section>
                @else
                    <div class="solar-card">
                        <h3 class="solar-card__title">No project calculated yet</h3>
                        <p class="solar-card__copy">
                            Fill the engineering form to generate a complete photovoltaic study with formulas, equipment sizing, cable and protection checks, charts, saved project data, and a PDF-ready report structure.
                        </p>
                    </div>
                @endif

                <section class="solar-card">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <h3 class="solar-card__title">Project management</h3>
                            <p class="solar-card__copy">Load a previous project into the form to edit it, or delete it directly from the engineer workspace.</p>
                        </div>
                    </div>

                    <div class="solar-list">
                        @forelse ($recentProjects as $savedProject)
                            <div class="solar-list-item">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">{{ $savedProject->name }}</div>
                                    <div class="text-sm text-slate-500">{{ $savedProject->city }} • {{ number_format($savedProject->required_kw, 2) }} kW • ROI {{ number_format($savedProject->roi_years, 1) }} years</div>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('engineer.dashboard', ['project' => $savedProject->id]) }}#engineer-form" class="solar-button-light">Edit</a>
                                    <a href="{{ route('engineer.dashboard', ['project' => $savedProject->id]) }}#project-results" class="solar-button-light">View</a>
                                    <form method="POST" action="{{ route('engineer.projects.destroy', $savedProject) }}" onsubmit="return confirm('Delete this project?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="solar-button-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="solar-empty">No saved projects yet.</div>
                        @endforelse
                    </div>
                </section>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const engineerForm = document.getElementById('engineer-form');
        const submitButton = document.getElementById('submit-button');
        const previewStatus = document.getElementById('preview-status');
        let previewTimeout = null;

        if (engineerForm) {
            engineerForm.addEventListener('submit', () => {
                submitButton.disabled = true;
                submitButton.textContent = 'Saving project...';
            });

            engineerForm.addEventListener('input', () => {
                clearTimeout(previewTimeout);
                previewTimeout = setTimeout(loadPreview, 550);
            });

            engineerForm.addEventListener('change', () => {
                clearTimeout(previewTimeout);
                previewTimeout = setTimeout(loadPreview, 300);
            });

            window.addEventListener('load', () => {
                if (canPreview()) {
                    loadPreview();
                }
            });
        }

        function canPreview() {
            const requiredIds = [
                'project_name',
                'city',
                'country',
                'daily_consumption',
                'peak_power',
                'day_usage_percent',
                'night_usage_percent',
                'tilt_angle',
                'azimuth',
                'available_surface',
                'temperature_min',
                'temperature_max',
                'cable_length',
                'price_per_kwh',
                'performance_ratio',
                'temperature_loss_percent',
                'inverter_loss_percent',
                'dust_loss_percent'
            ];

            const baseReady = requiredIds.every((id) => {
                const element = document.getElementById(id);
                return element && String(element.value).trim() !== '';
            });

            if (!baseReady) {
                return false;
            }

            const autoIrradiation = document.getElementById('use_auto_irradiation');
            const irradiation = document.getElementById('irradiation');

            if (autoIrradiation && String(autoIrradiation.value) === '0') {
                return irradiation && String(irradiation.value).trim() !== '';
            }

            return true;
        }

        async function loadPreview() {
            if (!canPreview()) {
                previewStatus.textContent = 'Complete required fields to preview';
                return;
            }

            previewStatus.textContent = 'Calculating...';

            const formData = new FormData(engineerForm);

            try {
                const response = await fetch('{{ route('engineer.preview') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (!response.ok) {
                    if (response.status === 422) {
                        const payload = await response.json();
                        const firstError = Object.values(payload.errors || {}).flat()[0];
                        throw new Error(firstError || 'Preview validation failed');
                    }
                    throw new Error('Preview failed');
                }

                const data = await response.json();
                document.getElementById('preview-required').textContent = Number(data.solar.required_kw).toFixed(2) + ' kW';
                document.getElementById('preview-real').textContent = Number(data.solar.real_kw).toFixed(2) + ' kW';
                document.getElementById('preview-cost').textContent = Number(data.costs.total).toFixed(2) + ' MAD';
                document.getElementById('preview-roi').textContent = Number(data.costs.roi_years).toFixed(1) + ' y';
                previewStatus.textContent = 'Preview updated';
            } catch (error) {
                previewStatus.textContent = error.message || 'Complete valid fields to preview';
            }
        }

        @if ($result)
            const monthlyProduction = @json(data_get($result, 'production.monthly_kwh', []));
            const annualProduction = Number(@json(data_get($result, 'production.yearly_kwh', 0)));
            const annualConsumption = Number(@json(data_get($result, 'input.daily_consumption', 0) * 365));
            const totalCost = Number(@json(data_get($result, 'costs.total', 0)));
            const annualSavings = Number(@json(data_get($result, 'costs.annual_savings', 0)));
            const tenYearProfit = Number(@json(data_get($result, 'costs.ten_year_profit', 0)));

            function buildChart(canvasId, config) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) {
                    return null;
                }

                return new Chart(canvas, config);
            }

            buildChart('energyBalanceChart', {
                type: 'bar',
                data: {
                    labels: ['Annual Consumption', 'Annual Production'],
                    datasets: [{
                        label: 'kWh/year',
                        data: [annualConsumption, annualProduction],
                        backgroundColor: ['#0ea5e9', '#f59e0b'],
                        borderRadius: 12,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Annual Production vs Consumption'
                        }
                    }
                }
            });

            buildChart('costBreakdownChart', {
                type: 'pie',
                data: {
                    labels: ['Panels', 'Inverters', 'DC Cables', 'AC Cables', 'Structure', 'Protection', 'Installation'],
                    datasets: [{
                        data: [
                            Number(@json(data_get($result, 'costs.panel', 0))),
                            Number(@json(data_get($result, 'costs.inverter', 0))),
                            Number(@json(data_get($result, 'costs.dc_cable', 0))),
                            Number(@json(data_get($result, 'costs.ac_cable', 0))),
                            Number({{ (float) data_get($result, 'costs.structure', 0) + (float) data_get($result, 'costs.massifs', 0) }}),
                            Number(@json(data_get($result, 'costs.protection', 0))),
                            Number(@json(data_get($result, 'costs.installation', 0)))
                        ],
                        backgroundColor: ['#f59e0b', '#0ea5e9', '#16a34a', '#22c55e', '#334155', '#94a3b8', '#f97316']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            buildChart('roiChart', {
                type: 'line',
                data: {
                    labels: ['Year 0', 'Year 1', 'Year 3', 'Year 5', 'Year 10'],
                    datasets: [{
                        label: 'Cumulative Return (MAD)',
                        data: [
                            -totalCost,
                            annualSavings - totalCost,
                            (annualSavings * 3) - totalCost,
                            (annualSavings * 5) - totalCost,
                            tenYearProfit
                        ],
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.14)',
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            buildChart('monthlyProductionChart', {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Monthly Production (kWh)',
                        data: monthlyProduction,
                        backgroundColor: '#f59e0b',
                        borderRadius: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        @endif
    </script>
</x-app-layout>
