<?php

namespace App\Services\Solar\Structure;

class StructureCalculator
{
    public function calculateStructure($panels, $panelLength = 2, $panelWidth = 1)
    {
        // assumption: 2 panels per row max (simple engineering model)
        $panelsPerRow = 2;

        $rows = ceil($panels / $panelsPerRow);

        // spacing between rows (meters)
        $rowSpacing = 1.2;

        $totalLength = $rows * ($panelLength + $rowSpacing);

        return [
            'rows' => $rows,
            'panels_per_row' => $panelsPerRow,
            'total_length_m' => round($totalLength, 2),
            'total_width_m' => $panelWidth * $panelsPerRow
        ];
    }

    public function windLoadFactor($locationWindSpeed = 120)
    {
        // simplified coefficient
        return $locationWindSpeed / 100;
    }
}