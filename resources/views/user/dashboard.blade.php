<style>
    :root {
        --bg: #0b1f33;
        --card: #0f273d;
        --accent: #f59e0b;
        --accent-2: #22c55e;
        --muted: #9ca3af;
        --text: #e5e7eb;
        --panel: #122c46;
        --border: #1f3b58;
        --font: "Segoe UI", sans-serif;
    }
    * { box-sizing: border-box; }
    body { margin: 0; padding: 0; background: radial-gradient(60% 60% at 20% 20%, rgba(245,158,11,0.08), transparent), var(--bg); color: var(--text); font-family: var(--font); }
    .wrap { max-width: 1050px; margin: 0 auto; padding: 20px 16px 36px; }
    .top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .title { display: flex; align-items: center; gap: 10px; }
    .sun { width: 32px; height: 32px; border-radius: 50%; background: radial-gradient(circle at 30% 30%, #facc15, #f59e0b); box-shadow: 0 0 14px rgba(245,158,11,0.5); }
    h1 { margin: 0; font-size: 22px; letter-spacing: 0.4px; }
    .logout-btn { background: transparent; color: var(--text); border: 1px solid var(--border); padding: 8px 12px; border-radius: 10px; cursor: pointer; transition: 0.2s ease; }
    .logout-btn:hover { border-color: var(--accent); color: var(--accent); }
    .card { background: var(--card); border: 1px solid var(--border); border-radius: 14px; padding: 16px; box-shadow: 0 10px 26px rgba(0,0,0,0.18); }
    .stack { display: grid; gap: 14px; }
    .layout { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; align-items: start; }
    form label { display: block; margin: 10px 0 6px; color: var(--muted); font-size: 13px; }
    form input, form select { width: 100%; background: var(--panel); border: 1px solid var(--border); color: var(--text); padding: 10px 12px; border-radius: 10px; outline: none; }
    form input:focus, form select:focus { border-color: var(--accent); }
    button[type="submit"] { background: linear-gradient(90deg, #f59e0b, #fbbf24); color: #0b1f33; border: none; padding: 12px; border-radius: 12px; font-weight: 700; cursor: pointer; width: 100%; box-shadow: 0 8px 20px rgba(245,158,11,0.28); margin-top: 14px; }
    .hr { height: 1px; background: var(--border); margin: 18px 0; }
    .row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .stat-box { background: var(--panel); border: 1px solid var(--border); border-radius: 10px; padding: 10px 12px; }
    .stat-box h3 { margin: 0; font-size: 13px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.4px; }
    .stat-box p { margin: 6px 0 0; font-size: 16px; font-weight: 700; }
    .download { margin-top: 10px; }
    .ghost { background: transparent; color: var(--text); border: 1px solid var(--border); box-shadow: none; }
    .ghost:hover { border-color: var(--accent); color: var(--accent); }
    .charts { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .chart-card { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; padding: 10px; }
    @media (max-width: 960px) {
        .layout { grid-template-columns: 1fr; }
        .charts { grid-template-columns: 1fr; }
    }
</style>

<div class="wrap">
<div class="top">
<div class="title">
            <div class="sun"></div>
    <div>
        <div style="color:#facc15;font-size:12px;letter-spacing:1px;">SOLAR PV</div>
        <h1>User Dashboard</h1>
    </div>
</div>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="logout-btn">Logout</button>
</form>
    </div>

    <div class="stack">
    <div class="layout">
    <div class="card">
    <form action="{{ route('user.calculate') }}" method="POST">
        @csrf

        <label>Month 1 (kWh):</label>
        <input type="number" name="m1" required>

        <label>Month 2 (kWh):</label>
        <input type="number" name="m2" required>

        <label>Month 3 (kWh):</label>
        <input type="number" name="m3" required>

        <label>Price per kWh (MAD):</label>
        <input type="number" name="price_kwh" value="1.5" step="0.1">

        <label>Region:</label>
        <select name="region">
            <option value="1.2">Strong sun (Oujda)</option>
            <option value="1.0">Average</option>
            <option value="0.8">Weak sun</option>
        </select>

        <label>Budget (MAD):</label>
        <input type="number" name="budget">

        <button type="submit">Calculate</button>
    </form>
    </div>
@if(isset($average))
<div class="card">
<div class="row">
    <div class="stat-box">
        <h3>Average (3 months)</h3>
        <p>{{ $average }} kWh</p>
    </div>
    <div class="stat-box">
        <h3>Required panels</h3>
        <p>{{ $panelCount }}</p>
    </div>
    <div class="stat-box">
        <h3>Total cost</h3>
        <p>{{ $totalCost }} MAD</p>
    </div>
    <div class="stat-box">
        <h3>Savings</h3>
        <p>{{ $savings }} MAD / month</p>
    </div>
    <div class="stat-box">
        <h3>Advice</h3>
        <p style="font-size:14px;">{{ $advice }}</p>
    </div>
    <div class="stat-box">
        <h3>Budget</h3>
        <p style="font-size:14px;">{{ $budgetStatus }}</p>
    </div>
    <div class="stat-box">
        <h3>Current cost</h3>
        <p>{{ $currentCost }} MAD</p>
    </div>
    <div class="stat-box">
        <h3>After solar</h3>
        <p>{{ $afterSolarCost }} MAD</p>
    </div>
</div>

    <div class="download">
        <form action="{{ route('user.report') }}" method="POST">
        @csrf
        <input type="hidden" name="m1" value="{{ $m1 }}">
        <input type="hidden" name="m2" value="{{ $m2 }}">
        <input type="hidden" name="m3" value="{{ $m3 }}">
        <input type="hidden" name="price_kwh" value="{{ $pricePerKwh }}">
        <button type="submit" class="ghost">Télécharger le rapport PDF</button>
        </form>
    </div>
</div>
@endif
</div>

<div class="card">
    <div class="charts">
        <div class="chart-card">
        <canvas id="consumptionChart"></canvas>
        </div>
        <div class="chart-card">
            <canvas id="savingsChart"></canvas>
        </div>
    </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('consumptionChart');
const barColor = ctx.getContext ? ctx.getContext('2d').createLinearGradient(0,0,0,200) : null;
if (barColor) { barColor.addColorStop(0, '#f59e0b'); barColor.addColorStop(1, '#fbbf24'); }

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Month 1', 'Month 2', 'Month 3'],
        datasets: [{
            label: 'Consumption (kWh)',
            data: [{{ $m1 ?? 0 }}, {{ $m2 ?? 0 }}, {{ $m3 ?? 0 }}],
            backgroundColor: barColor || '#f59e0b',
            borderRadius: 8
        }]
    },
    options: {
        scales: {
            y: { ticks: { color: '#cbd5e1' }, grid: { color: '#1f3b58' } },
            x: { ticks: { color: '#cbd5e1' }, grid: { display: false } }
        },
        plugins: { legend: { labels: { color: '#e5e7eb' } } }
    }
});

const ctx2 = document.getElementById('savingsChart');
new Chart(ctx2, {
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
            borderColor: '#22c55e',
            backgroundColor: 'rgba(34,197,94,0.18)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        scales: {
            y: { ticks: { color: '#cbd5e1' }, grid: { color: '#1f3b58' } },
            x: { ticks: { color: '#cbd5e1' }, grid: { display: false } }
        },
        plugins: { legend: { labels: { color: '#e5e7eb' } } }
    }
});
</script>
