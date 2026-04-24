<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans; color: #0f172a; font-size: 11px; line-height: 1.5; }
        h1, h2, h3 { margin: 0 0 8px; }
        h1 { text-align: center; margin-bottom: 16px; font-size: 22px; }
        h2 { font-size: 15px; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; }
        h3 { font-size: 12px; }
        .section { margin-bottom: 16px; }
        .box { border: 1px solid #cbd5e1; padding: 10px; border-radius: 8px; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { width: 50%; vertical-align: top; padding: 6px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .table th, .table td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; }
        .table th { background: #f8fafc; }
        .muted { color: #475569; }
        .pill { display: inline-block; padding: 4px 8px; border-radius: 999px; background: #f1f5f9; }
        ul { margin: 8px 0 0 18px; padding: 0; }
    </style>
</head>
<body>

<h1>Photovoltaic Engineering Report</h1>
<p>
    <strong>Project:</strong> {{ data_get($data, 'input.project_name', $project->name) }} |
    <strong>Location:</strong> {{ data_get($data, 'input.city') }}, {{ data_get($data, 'input.country') }} |
    <strong>ID:</strong> {{ $project->id }} |
    <strong>Date:</strong> {{ $project->created_at->format('Y-m-d H:i') }}
</p>

<div class="section box">
    <h2>1. Project Overview</h2>
    <table class="grid">
        <tr>
            <td>
                <p><strong>Installation Type:</strong> {{ data_get($data, 'input.installation_type') }}</p>
                <p><strong>Daily Consumption:</strong> {{ number_format(data_get($data, 'input.daily_consumption', 0), 2) }} kWh/day</p>
                <p><strong>Peak Power:</strong> {{ number_format(data_get($data, 'input.peak_power', 0), 2) }} kW</p>
                <p><strong>Day/Night Load Profile:</strong> {{ data_get($data, 'input.day_usage_percent') }}% / {{ data_get($data, 'input.night_usage_percent') }}%</p>
            </td>
            <td>
                <p><strong>Irradiation:</strong> {{ number_format(data_get($data, 'irradiation', 0), 0) }} kWh/m²/year</p>
                <p><strong>Tilt / Azimuth:</strong> {{ number_format(data_get($data, 'input.tilt_angle', 0), 1) }}° / {{ number_format(data_get($data, 'input.azimuth', 0), 1) }}°</p>
                <p><strong>Available Surface:</strong> {{ number_format(data_get($data, 'input.available_surface', 0), 1) }} m²</p>
                <p><strong>Global Status:</strong> <span class="pill">{{ ucfirst(data_get($data, 'global.status', 'n/a')) }}</span></p>
            </td>
        </tr>
    </table>
</div>

<div class="section box">
    <h2>2. Input Data and Losses</h2>
    <table class="table">
        <tr><th>Parameter</th><th>Value</th></tr>
        <tr><td>Performance Ratio</td><td>{{ number_format(data_get($data, 'input.performance_ratio', 0), 3) }}</td></tr>
        <tr><td>Temperature losses</td><td>{{ number_format(data_get($data, 'input.losses.temperature', 0), 1) }}%</td></tr>
        <tr><td>Inverter losses</td><td>{{ number_format(data_get($data, 'input.losses.inverter', 0), 1) }}%</td></tr>
        <tr><td>Dust losses</td><td>{{ number_format(data_get($data, 'input.losses.dust', 0), 1) }}%</td></tr>
        <tr><td>Other losses</td><td>{{ number_format(data_get($data, 'input.losses.other', 0), 1) }}%</td></tr>
        <tr><td>Degradation</td><td>{{ number_format(data_get($data, 'input.degradation', 0), 1) }}%</td></tr>
        <tr><td>Cable length</td><td>{{ number_format(data_get($data, 'input.cable_length', 0), 1) }} m</td></tr>
    </table>
</div>

<div class="section box">
    <h2>3. Detailed Calculations</h2>
    <p><strong>PV Power Formula:</strong> {{ data_get($data, 'formulas.pv_power') }}</p>
    <p><strong>Panel Count Formula:</strong> {{ data_get($data, 'formulas.panel_count') }}</p>
    <p><strong>Cable Sizing Formula:</strong> {{ data_get($data, 'formulas.cable_section') }}</p>
    <p><strong>ROI Formula:</strong> {{ data_get($data, 'formulas.roi') }}</p>

    <table class="table">
        <tr><th>Calculation</th><th>Result</th></tr>
        <tr><td>Required PV Power</td><td>{{ number_format(data_get($data, 'solar.required_kw', 0), 2) }} kW</td></tr>
        <tr><td>Installed PV Power</td><td>{{ number_format(data_get($data, 'solar.real_kw', 0), 2) }} kW</td></tr>
        <tr><td>Panel Count</td><td>{{ data_get($data, 'solar.panels', 0) }} modules</td></tr>
        <tr><td>Surface Used</td><td>{{ number_format(data_get($data, 'solar.surface_used_m2', 0), 2) }} m²</td></tr>
        <tr><td>DC/AC Ratio</td><td>{{ number_format(data_get($data, 'inverter.dc_ac_ratio', 0), 2) }}</td></tr>
        <tr><td>Annual Production</td><td>{{ number_format(data_get($data, 'production.yearly_kwh', 0), 0) }} kWh</td></tr>
    </table>
</div>

<div class="section">
    <table class="grid">
        <tr>
            <td>
                <div class="box">
                    <h2>4. System Design</h2>
                    <h3>Panels</h3>
                    <p>{{ data_get($data, 'panel.model') }} | {{ data_get($data, 'panel.efficiency') }}% | {{ data_get($data, 'panel.voc') }} Voc / {{ data_get($data, 'panel.vmp') }} Vmp</p>
                    <h3>Inverter</h3>
                    <p>{{ data_get($data, 'inverter.count') }} x {{ data_get($data, 'inverter.model') }} | {{ data_get($data, 'inverter.value') }} kW</p>
                    <h3>Stringing</h3>
                    <p>{{ data_get($data, 'stringing.strings') }} strings | {{ data_get($data, 'stringing.panels_per_string') }} panels/string | {{ data_get($data, 'stringing.mppt_used') }} MPPT used</p>
                </div>
            </td>
            <td>
                <div class="box">
                    <h2>5. Cables and Protection</h2>
                    <p><strong>DC Cable:</strong> {{ number_format(data_get($data, 'cable.dc.standard', 0), 2) }} mm² | Drop {{ number_format(data_get($data, 'cable.dc.voltage_drop_percent', 0), 2) }}%</p>
                    <p><strong>AC Cable:</strong> {{ number_format(data_get($data, 'cable.ac.standard', 0), 2) }} mm² | Drop {{ number_format(data_get($data, 'cable.ac.voltage_drop_percent', 0), 2) }}%</p>
                    <p><strong>Breaker:</strong> {{ data_get($data, 'protection.breaker') }} A</p>
                    <p><strong>Fuse:</strong> {{ data_get($data, 'protection.fuse') }} A</p>
                    <p><strong>SPD:</strong> {{ data_get($data, 'protection.spd') }} A</p>
                    <p><strong>Earthing:</strong> {{ data_get($data, 'protection.earthing') }} A</p>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="section box">
    <h2>6. Structure and Civil</h2>
    <p><strong>Mounting:</strong> {{ data_get($data, 'structure.mounting_type') }} / {{ data_get($data, 'structure.structure_type') }}</p>
    <p><strong>Rows:</strong> {{ data_get($data, 'structure.rows') }} | <strong>Panels per Row:</strong> {{ data_get($data, 'structure.panels_per_row') }}</p>
    <p><strong>Footprint:</strong> {{ number_format(data_get($data, 'structure.footprint_m2', 0), 2) }} m² | <strong>Surface Usage:</strong> {{ number_format(data_get($data, 'structure.surface_usage_percent', 0), 1) }}%</p>
    <p><strong>Wind Pressure:</strong> {{ number_format(data_get($data, 'structure.wind_pressure_kN_m2', 0), 2) }} kN/m²</p>
    <p><strong>Weight Distribution:</strong> {{ number_format(data_get($data, 'structure.weight_distribution_kg_m2', 0), 2) }} kg/m²</p>
    <p><strong>Massifs:</strong> {{ data_get($data, 'massifs.count') }} | <strong>Concrete Volume:</strong> {{ number_format(data_get($data, 'massifs.concrete_volume_m3', 0), 2) }} m³</p>
</div>

<div class="section box">
    <h2>7. Financial Analysis</h2>
    <table class="table">
        <tr><th>Item</th><th>Value</th></tr>
        <tr><td>Panels cost</td><td>{{ number_format(data_get($data, 'costs.panel', 0), 2) }} MAD</td></tr>
        <tr><td>Inverter cost</td><td>{{ number_format(data_get($data, 'costs.inverter', 0), 2) }} MAD</td></tr>
        <tr><td>Cables cost</td><td>{{ number_format(data_get($data, 'costs.cable', 0), 2) }} MAD</td></tr>
        <tr><td>Structure cost</td><td>{{ number_format(data_get($data, 'costs.structure', 0), 2) }} MAD</td></tr>
        <tr><td>Protection cost</td><td>{{ number_format(data_get($data, 'costs.protection', 0), 2) }} MAD</td></tr>
        <tr><td>Installation cost</td><td>{{ number_format(data_get($data, 'costs.installation', 0), 2) }} MAD</td></tr>
        <tr><td>Total investment</td><td>{{ number_format(data_get($data, 'costs.total', 0), 2) }} MAD</td></tr>
        <tr><td>Annual production</td><td>{{ number_format(data_get($data, 'production.yearly_kwh', 0), 0) }} kWh</td></tr>
        <tr><td>Annual savings</td><td>{{ number_format(data_get($data, 'costs.annual_savings', 0), 2) }} MAD</td></tr>
        <tr><td>ROI</td><td>{{ number_format(data_get($data, 'costs.roi_years', 0), 1) }} years</td></tr>
        <tr><td>Payback period</td><td>{{ number_format(data_get($data, 'costs.payback_years', 0), 1) }} years</td></tr>
        <tr><td>10-year profit</td><td>{{ number_format(data_get($data, 'costs.ten_year_profit', 0), 2) }} MAD</td></tr>
    </table>
</div>

<div class="section box">
    <h2>8. Scenario Comparison and Recommendation</h2>
    @if(!empty($comparison))
        <ul>
            @foreach($comparison as $scenario)
                <li>{{ $scenario['panel_model'] }} ({{ $scenario['panel_power'] }} W) | {{ $scenario['panels'] }} panels | {{ number_format($scenario['total_cost'], 2) }} MAD | ROI {{ number_format($scenario['roi_years'], 1) }} years</li>
            @endforeach
        </ul>
    @else
        <p>No scenario comparison available.</p>
    @endif

    @if($recommendedScenario)
        <p><strong>Recommended Scenario:</strong> {{ $recommendedScenario['panel_model'] }} with ROI {{ number_format($recommendedScenario['roi_years'], 1) }} years and total investment {{ number_format($recommendedScenario['total_cost'], 2) }} MAD.</p>
    @endif
</div>

<div class="section box">
    <h2>9. Final Recommendation</h2>
    <p class="muted">The following engineering comments were generated automatically from the project checks and financial results:</p>
    <ul>
        @forelse(data_get($data, 'suggestions', []) as $suggestion)
            <li>{{ $suggestion }}</li>
        @empty
            <li>The selected design is acceptable for a preliminary photovoltaic engineering study.</li>
        @endforelse
    </ul>
</div>

</body>
</html>
