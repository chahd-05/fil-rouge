@php
    $maxMonth = max($m1, $m2, $m3, 1);
    $monthBars = [
        ['label' => 'Month 1', 'value' => $m1],
        ['label' => 'Month 2', 'value' => $m2],
        ['label' => 'Month 3', 'value' => $m3],
    ];
    $costBars = [
        ['label' => 'Current bill', 'value' => $currentCost],
        ['label' => 'After solar', 'value' => $afterSolarCost],
    ];
    $maxCost = max($currentCost, $afterSolarCost, 1);
@endphp

<style>
    body { font-family: Arial, sans-serif; color: #1f2937; }
    h1 { color: #0f172a; margin-bottom: 8px; }
    h2 { margin: 16px 0 6px; }
    .section { margin-top: 14px; padding-top: 10px; border-top: 1px solid #e5e7eb; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .stat { background: #f8fafc; padding: 10px 12px; border-radius: 8px; }
    .bar { height: 14px; border-radius: 6px; background: linear-gradient(90deg, #22c55e, #16a34a); }
    .bar-wrap { background: #e5e7eb; border-radius: 8px; padding: 4px; margin-bottom: 8px; }
    .bar-label { font-size: 12px; color: #475569; display: flex; justify-content: space-between; }
    .note { font-size: 12px; color: #64748b; }
</style>

<h1>Solar Report</h1>
<p class="note">Tarif utilisé: {{ number_format($pricePerKwh, 2) }} MAD / kWh</p>

<div class="grid">
    <div class="stat">
        <strong>M1</strong> : {{ number_format($m1, 2) }} kWh
    </div>
    <div class="stat">
        <strong>M2</strong> : {{ number_format($m2, 2) }} kWh
    </div>
    <div class="stat">
        <strong>M3</strong> : {{ number_format($m3, 2) }} kWh
    </div>
    <div class="stat">
        <strong>Moyenne</strong> : {{ number_format($average, 2) }} kWh
    </div>
</div>

<div class="section">
    <h2>Consommation (graphique)</h2>
    @foreach($monthBars as $bar)
        @php $width = ($bar['value'] / $maxMonth) * 100; @endphp
        <div class="bar-label">
            <span>{{ $bar['label'] }}</span>
            <span>{{ number_format($bar['value'], 2) }} kWh</span>
        </div>
        <div class="bar-wrap">
            <div class="bar" style="width: {{ $width }}%;"></div>
        </div>
    @endforeach
    <p class="note">Échelle ajustée sur votre mois le plus élevé.</p>
</div>

<div class="section">
    <h2>Coût mensuel</h2>
    <div class="grid">
        <div class="stat"><strong>Avant solaire</strong><br>{{ number_format($currentCost, 2) }} MAD</div>
        <div class="stat"><strong>Après solaire</strong><br>{{ number_format($afterSolarCost, 2) }} MAD</div>
    </div>
    @foreach($costBars as $bar)
        @php $width = ($bar['value'] / $maxCost) * 100; @endphp
        <div class="bar-label">
            <span>{{ $bar['label'] }}</span>
            <span>{{ number_format($bar['value'], 2) }} MAD</span>
        </div>
        <div class="bar-wrap">
            <div class="bar" style="width: {{ $width }}%;"></div>
        </div>
    @endforeach
</div>

<div class="section">
    <h2>Économies</h2>
    <p><strong>Mensuel:</strong> {{ number_format($savings, 2) }} MAD</p>
    <p><strong>Annuel:</strong> {{ number_format($yearlySavings, 2) }} MAD</p>
    <p><strong>Sur 10 ans:</strong> {{ number_format($savings10Years, 2) }} MAD</p>
</div>
