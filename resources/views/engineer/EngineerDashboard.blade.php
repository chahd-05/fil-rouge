<!DOCTYPE html>
<html>
<head>
    <title>Engineer Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h1>Solar Production Dashboard</h1>

<canvas id="productionChart" width="600" height="300"></canvas>


<h2>Profit Analysis</h2>

<canvas id="revenueChart" width="600" height="300"></canvas>
<canvas id="profitChart" width="600" height="300"></canvas>

<script>
    
    const monthlyData = @json($data['production']['monthly_kwh']);
    const yearlyProduction = {{ $yearlyProduction }};
    const totalCost = {{ $totalCost }};
    const pricePerKwh = 1.2;

    const productionCtx = document.getElementById('productionChart').getContext('2d');

    new Chart(productionCtx, {
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

    const years = 10;

    let revenueData = [];
    let cumulativeProfit = [];

    let total = 0;

    for (let i = 1; i <= years; i++) {
        let yearlyRevenue = yearlyProduction * pricePerKwh;

        total += yearlyRevenue;

        revenueData.push(yearlyRevenue);
        cumulativeProfit.push(total - totalCost);
    }

    const revenueCtx = document.getElementById('revenueChart').getContext('2d');

    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: Array.from({length: years}, (_, i) => 'Year ' + (i+1)),
            datasets: [{
                label: 'Annual Revenue (MAD)',
                data: revenueData
            }]
        }
    });

    const profitCtx = document.getElementById('profitChart').getContext('2d');

    new Chart(profitCtx, {
        type: 'line',
        data: {
            labels: Array.from({length: years}, (_, i) => 'Year ' + (i+1)),
            datasets: [{
                label: 'Cumulative Profit (MAD)',
                data: cumulativeProfit,
                fill: false
            }]
        }
    });

</script>

</body>
</html>