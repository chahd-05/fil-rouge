<?php

namespace App\Services\Solar\Structure;

class MassifCalculator
{
    public function calculateMassifs($panels, $rows)
    {
        // assumption: 1 support every 2 panels + row ends
        $supportsPerRow = ceil($panels / $rows);

        $totalMassifs = $rows * ($supportsPerRow + 1);

        return [
            'massifs_count' => $totalMassifs
        ];
    }

    public function concreteVolume($massifsCount, $size = 0.4)
    {
        // cubic concrete per massif (m³)
        $volumePerMassif = $size * $size * $size;

        return round($massifsCount * $volumePerMassif, 2);
    }
}