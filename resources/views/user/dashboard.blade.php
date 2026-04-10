<h1>User Dashboard</h1>

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

@if(isset($average))

    <hr>

    <h2>Result</h2>

    <p>Average (3 months): {{ $average }} kWh</p>
    <p>Required panels: {{ $panelCount }}</p>
    <p>Total cost: {{ $totalCost }} MAD</p>

    <h3>Savings</h3>
    <p>{{ $savings }} MAD / month</p>

    <h3>Advice</h3>
    <p>{{ $advice }}</p>

    <h3>Comparison</h3>
    <p>Current cost: {{ $currentCost }} MAD</p>
    <p>After solar: {{ $afterSolarCost }} MAD</p>
    <p>Savings: {{ $savings }} MAD/month</p>

    <h3>Budget</h3>
    <p>{{ $budgetStatus }}</p>

    <form action="{{ route('user.report') }}" method="POST" style="margin-top: 12px;">
        @csrf
        <input type="hidden" name="m1" value="{{ $m1 }}">
        <input type="hidden" name="m2" value="{{ $m2 }}">
        <input type="hidden" name="m3" value="{{ $m3 }}">
        <input type="hidden" name="price_kwh" value="{{ $pricePerKwh }}">
        <button type="submit">Télécharger le rapport PDF</button>
    </form>

@endif

<hr>

<canvas id="consumptionChart"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('consumptionChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Month 1', 'Month 2', 'Month 3'],
        datasets: [{
            label: 'Consumption (kWh)',
            data: [{{ $m1 ?? 0 }}, {{ $m2 ?? 0 }}, {{ $m3 ?? 0 }}],
        }]
    }
});
</script>

<canvas id="savingsChart"></canvas>

<script>
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
            ]
        }]
    }
});
</script>
