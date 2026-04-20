<!DOCTYPE html>
<html>
<head>
    <title>Engineer Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h1>Solar Production Dashboard</h1>

<canvas id="productionChart" width="600" height="300"></canvas>

<script>
    const monthlyData = @json($data['production']['monthly_kwh']);

    const ctx = document.getElementById('productionChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                'Jan','Feb','Mar','Apr','May','Jun',
                'Jul','Aug','Sep','Oct','Nov','Dec'
            ],
            datasets: [{
                label: 'Monthly Production (kWh)',
                data: monthlyData
            }]
        }
    });

    new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            'Jan','Feb','Mar','Apr','May','Jun',
            'Jul','Aug','Sep','Oct','Nov','Dec'
        ],
        datasets: [{
            label: 'Production Trend',
            data: monthlyData,
            fill: false
        }]
    }
});
</script>

</body>
</html>