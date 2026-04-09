<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view ('user.dashboard');
    }

    public function calculate(Request $request) {
       $m1 = $request->m1;
       $m2 = $request->m2;
       $m3 = $request->m3;

       $average = ($m1 + $m2 + $m3) / 3;

       $dailyConsumption = $average / 30;

       $panelPower = 400;
       $sunHours = 5;

       $neededPower = $dailyConsumption / $sunHours;

       $panelCount = ceil(($neededPower * 1000) / $panelPower);

       $pricePerPanel = 1500; 
       $totalCost = $panelCount * $pricePerPanel;


       $afterSolar = $average * 0.4;

       $pricePerKwh = 1.5;
       $savings = ($average - $afterSolar) * $pricePerKwh;

       if ($average > 400) {
        $advice = "Solar installation highly recommended";
       }
       elseif ($average > 200) {
        $advice = "Installation recommanded";
       }
       else {
         $advice = "Weak consumption";
       }
    return view ('user.dashboard', compact(
        'panelCount',
        'totalCost',
        'average',
        'advice',
        'afterSolar',
        'pricePerKwh',
        'savings'
       ));


    }

}
