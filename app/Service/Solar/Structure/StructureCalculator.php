<?php

namespace App\Services\Solar\Structure;

class StructureCalculator
{
    public function calculateStructure(array $context): array
    {
        $panelCount = (int) $context['panels'];
        $panelLength = (float) $context['panel_length_m'];
        $panelWidth = (float) $context['panel_width_m'];
        $tilt = (float) $context['tilt_angle'];
        $mountingType = $context['mounting_type'];
        $structureType = $context['structure_type'];
        $availableSurface = (float) $context['available_surface'];

        $panelsPerRow = $mountingType === 'ground' ? 4 : 2;
        $rows = (int) ceil($panelCount / max($panelsPerRow, 1));
        $trackingMultiplier = $structureType === 'tracking' ? 1.15 : 1;
        $rowSpacing = $mountingType === 'ground'
            ? max(1.6, ($panelLength * sin(deg2rad(max($tilt, 5)))) + 1.1)
            : 0.4;

        $totalWidth = round($panelsPerRow * $panelWidth, 2);
        $totalLength = round($rows * ($panelLength + $rowSpacing), 2);
        $footprint = round($totalWidth * $totalLength * $trackingMultiplier, 2);
        $surfaceUsageRatio = $availableSurface > 0 ? round(($footprint / $availableSurface) * 100, 1) : 0;
        $weightPerPanelKg = $mountingType === 'ground' ? 34 : 28;

        return [
            'mounting_type' => $mountingType,
            'structure_type' => $structureType,
            'rows' => $rows,
            'panels_per_row' => $panelsPerRow,
            'row_spacing_m' => round($rowSpacing, 2),
            'total_length_m' => $totalLength,
            'total_width_m' => $totalWidth,
            'footprint_m2' => $footprint,
            'surface_usage_percent' => $surfaceUsageRatio,
            'optimized_tilt_deg' => $this->optimizedTilt((float) $context['latitude'], $structureType),
            'weight_distribution_kg_m2' => round(($panelCount * $weightPerPanelKg) / max($footprint, 1), 2),
        ];
    }

    public function optimizedTilt(float $latitude, string $structureType): float
    {
        $base = abs($latitude) * 0.76;

        if ($structureType === 'tracking') {
            $base -= 5;
        }

        return round(max(min($base, 35), 10), 1);
    }

    public function windLoadFactor(float $windSpeedKmH = 120): float
    {
        return round(0.613 * pow($windSpeedKmH / 3.6, 2) / 1000, 2);
    }
}
