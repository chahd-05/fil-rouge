<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans; }
        h1 { text-align: center; }
        .section { margin-bottom: 20px; }
        .box { border: 1px solid #ccc; padding: 10px; }
    </style>
</head>
<body>

<h1>Solar Installation Report</h1>

<div class="section box">
    <h2>Solar</h2>
    <p>Required Power: {{ $data['solar']['required_kw'] }} kW</p>
    <p>Real Power: {{ $data['solar']['real_kw'] }} kW</p>
    <p>Panels: {{ $data['solar']['panels'] }}</p>
</div>

<div class="section box">
    <h2>Inverter</h2>
    <p>Power: {{ $data['inverter']['value'] }} kW</p>
    <p>Status: {{ $data['inverter']['validation']['status'] }}</p>
</div>

<div class="section box">
    <h2>Cable</h2>
    <p>Section: {{ $data['cable']['section'] }} mm²</p>
    <p>Standard: {{ $data['cable']['standard'] }} mm²</p>
</div>

<div class="section box">
    <h2>Protection</h2>
    <p>Breaker: {{ $data['protection']['breaker'] }} A</p>
    <p>Fuse: {{ $data['protection']['fuse'] }} A</p>
</div>

<div class="section box">
    <h2>Structure</h2>
    <p>Rows: {{ $data['structure']['rows'] }}</p>
    <p>Length: {{ $data['structure']['total_length_m'] }} m</p>
</div>

<div class="section box">
    <h2>Massifs</h2>
    <p>Count: {{ $data['massifs']['count'] }}</p>
    <p>Concrete: {{ $data['massifs']['concrete_volume_m3'] }} m³</p>
</div>

<div class="section box">
    <h2>Costs</h2>
    <p>Total: {{ $data['costs']['total'] }} MAD</p>
    <p>ROI: {{ $data['costs']['roi_years'] }} years</p>
</div>

<div class="section box">
    <h2>Conclusion</h2>
    <p>Status: {{ $data['global']['status'] }}</p>
</div>

</body>
</html>