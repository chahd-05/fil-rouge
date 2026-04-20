<!DOCTYPE html>
<html>
<head>
    <title>Engineer Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
    body {
        font-family: Arial;
        background: #0f172a;
        color: white;
        text-align: center;
    }

    h1, h2 {
        color: #38bdf8;
    }

    canvas {
        background: #1e293b;
        margin: 20px auto;
        padding: 10px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        display: block;
    }
    </style>
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

    const years = 10;
    const degradationRate = 0.99;

    const productionCtx = document.getElementById('productionChart').getContext('2d');

    new Chart(productionCtx, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                label: 'Monthly Production (kWh)',
                data: monthlyData
            }]
        }
    });

    let revenueData = [];
    let cumulativeProfit = [];
    let total = 0;

    let currentProduction = yearlyProduction;

    for (let i = 1; i <= years; i++) {
        let yearlyRevenue = currentProduction * pricePerKwh;
        total += yearlyRevenue;
        revenueData.push(yearlyRevenue);
        cumulativeProfit.push(total - totalCost);
        currentProduction *= degradationRate;
    }

    let breakEvenYear = null;

    for (let i = 0; i < cumulativeProfit.length; i++) {
        if (cumulativeProfit[i] >= 0) {
            breakEvenYear = i + 1;
            break;
        }
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
            datasets: [
                {
                    label: 'Cumulative Profit (MAD)',
                    data: cumulativeProfit,
                    fill: false
                },
                {
                    label: 'Break-even Line',
                    data: Array(years).fill(0),
                    borderColor: 'red',
                    borderDash: [5, 5]
                }
            ]
        }
    });
</script>

</body>
</html>