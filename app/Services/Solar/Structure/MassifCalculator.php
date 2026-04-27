<?php

namespace App\Services\Solar\Structure;

class MassifCalculator
{
    public function calculateMassifs(int $panels, int $rows, string $mountingType): array
    {
        if ($mountingType === 'roof') {
            return [
                'massifs_count' => 0,
                'supports_per_row' => 0,
            ];
        }

        $panelsPerRow = max(1, (int) ceil($panels / max($rows, 1)));
        $supportsPerRow = $mountingType === 'ground'
            ? max(3, (int) ceil($panelsPerRow / 3) + 2)
            : 0;

        $massifsCount = $rows * $supportsPerRow;

        return [
            'massifs_count' => $massifsCount,
            'supports_per_row' => $supportsPerRow,
        ];
    }

    public function concreteVolume(int $massifsCount, float $blockSide = 0.45, float $depth = 0.6): float
    {
        if ($massifsCount <= 0) {
            return 0;
        }

        return round($massifsCount * $blockSide * $blockSide * $depth, 2);
    }
}
