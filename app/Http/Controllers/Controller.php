<?php
use Barryvdh\DomPDF\Facade\Pdf;


public function downloadPDF(Request $request)
{
    $m1 = (float) $request->m1;
    $m2 = (float) $request->m2;
    $m3 = (float) $request->m3;

    $average = ($m1 + $m2 + $m3) / 3;
    $afterSolar = $average * 0.4;

    $pricePerKwh = (float) $request->price_kwh;

    $currentCost = $average * $pricePerKwh;
    $afterSolarCost = $afterSolar * $pricePerKwh;
    $savings = $currentCost - $afterSolarCost;

    $pdf = Pdf::loadView('user.report', compact(
        'm1','m2','m3',
        'average',
        'afterSolar',
        'currentCost',
        'afterSolarCost',
        'savings'
    ));

    return $pdf->download('solar-report.pdf');
}