<form action="{{ route('user.report') }}" method="POST">
    @csrf

    <input type="hidden" name="m1" value="{{ $m1 ?? 0 }}">
    <input type="hidden" name="m2" value="{{ $m2 ?? 0 }}">
    <input type="hidden" name="m3" value="{{ $m3 ?? 0 }}">
    <input type="hidden" name="price_kwh" value="{{ $pricePerKwh ?? 1.5 }}">

    <button type="submit">Download PDF</button>
</form>

<h1>Solar Report</h1>

<p>Month 1: {{ $m1 }}</p>
<p>Month 2: {{ $m2 }}</p>
<p>Month 3: {{ $m3 }}</p>

<hr>

<p>Average: {{ $average }} kWh</p>
<p>After solar: {{ $afterSolar }} kWh</p>

<hr>

<p>Current cost: {{ $currentCost }} MAD</p>
<p>After solar cost: {{ $afterSolarCost }} MAD</p>

<h3>Savings: {{ $savings }} MAD / month</h3>