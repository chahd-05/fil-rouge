<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('user.dashboard');
    }

    public function calculate(Request $request)
    {
        $m1 = (float) $request->m1;
        $m2 = (float) $request->m2;
        $m3 = (float) $request->m3;

        $pricePerKwh = (float) $request->price_kwh;
        $regionFactor = (float) $request->region;
        $budget = (float) $request->budget;

        $average = ($m1 + $m2 + $m3) / 3;

        $dailyConsumption = $average / 30;

        $panelPower = 400;
        $sunHours = 5;

        $neededPower = ($dailyConsumption / $sunHours) * $regionFactor;

        $panelCount = ceil(($neededPower * 1000) / $panelPower);

        $pricePerPanel = 1500;
        $totalCost = $panelCount * $pricePerPanel;

        $afterSolar = $average * 0.4;

        $currentCost = $average * $pricePerKwh;
        $afterSolarCost = $afterSolar * $pricePerKwh;

        $savings = $currentCost - $afterSolarCost;

        if ($average > 400) {
            $advice = "Installation fortement recommandée 🔥";
        } elseif ($average > 200) {
            $advice = "Installation recommandée";
        } else {
            $advice = "Consommation faible";
        }

        if ($budget >= $totalCost) {
            $budgetStatus = "Budget sufficient";
        } else {
            $budgetStatus = "Budget insufficient";
        }

        return view('user.dashboard', compact(
            'm1', 'm2', 'm3',
            'average',
            'panelCount',
            'totalCost',
            'afterSolar',
            'currentCost',
            'afterSolarCost',
            'savings',
            'advice',
            'budgetStatus',
            'pricePerKwh'
        ));
    }
}