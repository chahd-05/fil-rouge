<h1>User dashboard</h1>

<form action="{{ route('user.calculate') }}" method="POST">
@csrf

    <label>Month 1 (kWh):</label>
    <input type="number" name="m1" required>

    <label>Month 2 (kWh):</label>
    <input type="number" name="m2" required>

    <label>Month 3 (kWh):</label>
    <input type="number" name="m3" required>

    <label> price per KWh (MAD):</label>
    <input type="number" name="price_Kwh" value="1.5" step="0.1">

    <label>Region:</label>
    <select name="region">
    <option value="1.2">Strong sun (Oujda)</option>
    <option value="1.0">Average</option>
    <option value="0.8">Weak sunl</option>
    </select>

    <button type="submit">Calculate</button>
</form>

@if(isset($average))
    <h2>Result:</h2>
    <p>Average (3 months) {{$average}} KWh</p>
    <p>required panels: {{$panelCount}}</p>
    <p>Estimated cost: {{ $totalCost}} MAD</p>

    <h3>Savings</h3>
    <p>You save around: {{ $savings }} MAD / month</p>

    <h3>Advice</h3>
    <p>{{$advice}}</p>

    <h3>comparing</h3>
    <p>Current cost: {{ $currentCost }}</p>
    <p>After solar: {{ $afterSolarCost}}</p>
    <p>Saving: {{ $savings }} MAD/month</p>
@endif



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<canvas id="consumptionChart"></canvas>
 <script>
    const ctx = document.getElementById('consumptionChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Month 1', 'Month 2', 'Month 3'],
            datasets: [{
                label: 'consumption (KWh)',
                data: [{{ $m1 ?? 0 }}, {{ $m2 ?? 0 }}, {{ $m3 ?? 0 }}],
            }]
        }
    });
 </script>