<!DOCTYPE html>
<html>
<head>
    <title>Project Details</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial;
            background: #0f172a;
            color: white;
            padding: 20px;
        }

        h1, h2 {
            color: #38bdf8;
        }

        .card {
            background: #1e293b;
            padding: 20px;
            margin: 15px 0;
            border-radius: 12px;
        }

        canvas {
            background: #1e293b;
            margin-top: 20px;
            padding: 10px;
            border-radius: 12px;
        }
    </style>
</head>
<body>

<h1>Project: {{ $project->city }}</h1>

<div class="card">
    <h2>Solar System</h2>
    <p>Required Power: {{ $project->required_kw }} kW</p>
    <p>Real Power: {{ $project->real_kw }} kW</p>
    <p>Panels: {{ $project->panels }}</p>
</div>

<div class="card">
    <h2>Production</h2>
    <p>Yearly: {{ $project->production['yearly'] }} kWh</p>

    <canvas id="productionChart"></canvas>
</div>

<div class="card">
    <h2>Costs</h2>
    <p>Total Cost: {{ $project->costs['total'] }} MAD</p>
    <p>ROI: {{ $project->roi_years }} years</p>
</div>

<script>
    const monthlyData = @json($project->production['monthly']);

    new Chart(document.getElementById('productionChart'), {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                label: 'Monthly Production (kWh)',
                data: monthlyData
            }]
        }
    });
</script>

</body>
</html>