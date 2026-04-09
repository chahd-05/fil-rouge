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

@if(isset($panelCount))
    <h2>Result:</h2>
    <p>Panels needed: {{ $panelCount }}</p>
    <p>Estimate Cost: {{ $totalCost }} MAD</p>
@endif