<h1>User dashboard</h1>

<form action="{{ route('user.calculate') }}" method="POST">
@csrf

    <label>Month 1 (kWh):</label>
    <input type="number" name="m1" required>

    <label>Month 2 (kWh):</label>
    <input type="number" name="m2" required>

    <label>Month 3 (kWh):</label>
    <input type="number" name="m3" required>

    <button type="submit">Calculate</button>
</form>

@if(isset($average))
    <h2>Result:</h2>
    <p>Average (3 months) {{$average}} KWh</p>
    <p>required panels: {{$panelCount}}</p>
    <p>Estimated cost: {{ $totalCost}} MAD</p>

    <h3>Comparing</h3>
    <p>Before solar: {{$average}} KWh</p>
    <p>After solar: {{$afterSolar}} KWh</p>

    <h3>Savings</h3>
    <p>You save around: {{ $savings }} MAD / month</p>

    <h3>Advice</h3>
    <p>{{$advice}}</p>
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