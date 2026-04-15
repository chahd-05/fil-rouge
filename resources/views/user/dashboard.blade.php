<style>
    body {
    margin: 0;
    padding: 0;
    background: radial-gradient(60% 60% at 20% 20%, rgba(245,158,11,0.08), transparent), #0b1f33;
    color: #e5e7eb;
    font-family: "Segoe UI", sans-serif;
}

.wrap {
    max-width: 1050px;
    margin: auto;
    padding: 20px 16px 36px;
}

.top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.sun {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: radial-gradient(circle at 30% 30%, #facc15, #f59e0b);
    box-shadow: 0 0 14px rgba(245,158,11,0.5);
}

h1 {
    margin: 0;
    font-size: 22px;
}

.logout-btn {
    background: transparent;
    color: #e5e7eb;
    border: 1px solid #1f3b58;
    padding: 8px 12px;
    border-radius: 10px;
    cursor: pointer;
}

.logout-btn:hover {
    border-color: #f59e0b;
    color: #f59e0b;
}

.card {
    background: #0f273d;
    border: 1px solid #1f3b58;
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 14px;
}

.layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

label {
    display: block;
    margin-top: 10px;
    font-size: 13px;
    color: #9ca3af;
}

input, select {
    width: 100%;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #1f3b58;
    background: #122c46;
    color: #e5e7eb;
}

input:focus, select:focus {
    border-color: #f59e0b;
    outline: none;
}

button {
    margin-top: 14px;
    width: 100%;
    padding: 12px;
    border-radius: 12px;
    border: none;
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
    color: #0b1f33;
    font-weight: bold;
    cursor: pointer;
}

.row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.stat-box {
    background: #122c46;
    border: 1px solid #1f3b58;
    border-radius: 10px;
    padding: 10px;
}

.stat-box h3 {
    margin: 0;
    font-size: 13px;
    color: #9ca3af;
}

.stat-box p {
    margin-top: 6px;
    font-weight: bold;
}

.charts {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.chart-card {
    background: #122c46;
    border: 1px solid #1f3b58;
    border-radius: 12px;
    padding: 10px;
}

@media (max-width: 960px) {
    .layout {
        grid-template-columns: 1fr;
    }

    .charts {
        grid-template-columns: 1fr;
    }
}
.video-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: -2;
}

.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(11, 31, 51, 0.75);
    z-index: -1;
}
</style>

<div class="wrap">

<div class="top">
<div class="title">
<div class="sun"></div>
<div>
<div style="color:#facc15;font-size:12px;">SOLAR PV</div>
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
<input type="number" name="price_kwh" value="1.5">

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
<h3>Average</h3>
<p>{{ $average }} kWh</p>
</div>

<div class="stat-box">
<h3>Panels</h3>
<p>{{ $panelCount }}</p>
</div>

<div class="stat-box">
<h3>Cost</h3>
<p>{{ $totalCost }} MAD</p>
</div>

<div class="stat-box">
<h3>Savings</h3>
<p>{{ $savings }} MAD</p>
</div>

<div class="stat-box">
<h3>Advice</h3>
<p>{{ $advice }}</p>
</div>

<div class="stat-box">
<h3>Budget</h3>
<p>{{ $budgetStatus }}</p>
</div>

<div class="stat-box">
<h3>Before</h3>
<p>{{ $currentCost }} MAD</p>
</div>

<div class="stat-box">
<h3>After</h3>
<p>{{ $afterSolarCost }} MAD</p>
</div>

</div>

<div style="margin-top:10px;">
<form action="{{ route('user.report') }}" method="POST">
@csrf
<input type="hidden" name="m1" value="{{ $m1 }}">
<input type="hidden" name="m2" value="{{ $m2 }}">
<input type="hidden" name="m3" value="{{ $m3 }}">
<input type="hidden" name="price_kwh" value="{{ $pricePerKwh }}">
<button type="submit">Download PDF</button>
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

let ctx = document.getElementById('consumptionChart');

let gradient = null;
if(ctx.getContext){
let g = ctx.getContext('2d');
gradient = g.createLinearGradient(0,0,0,200);
gradient.addColorStop(0,'#f59e0b');
gradient.addColorStop(1,'#fbbf24');
}

new Chart(ctx,{
type:'bar',
data:{
labels:['Month 1','Month 2','Month 3'],
datasets:[{
label:'Consumption',
data:[{{ $m1 ?? 0 }},{{ $m2 ?? 0 }},{{ $m3 ?? 0 }}],
backgroundColor:gradient || '#f59e0b'
}]
}
});

let ctx2 = document.getElementById('savingsChart');

new Chart(ctx2,{
type:'line',
data:{
labels:['1 Year','5 Years','10 Years'],
datasets:[{
label:'Savings',
data:[
{{ $yearlySavings ?? 0 }},
{{ ($yearlySavings ?? 0)*5 }},
{{ $savings10Years ?? 0 }}
],
borderColor:'#22c55e',
backgroundColor:'rgba(34,197,94,0.2)',
fill:true
}]
}
});

</script>
