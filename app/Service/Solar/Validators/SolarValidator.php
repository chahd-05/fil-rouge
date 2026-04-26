<?php

namespace App\Services\Solar\Validators;

class SolarValidator
{
    public function surface(float $usedSurface, float $availableSurface): array
    {
        if ($usedSurface > $availableSurface) {
            return ['status' => 'insufficient', 'message' => 'Available surface is insufficient for the selected array.'];
        }

        return ['status' => 'valid', 'message' => 'Available surface is compatible with the design.'];
    }

    public function inverter(float $dcPowerKw, float $acPowerKw): array
    {
        $ratio = $acPowerKw > 0 ? $dcPowerKw / $acPowerKw : 0;

        if ($ratio < 0.9) {
            return ['status' => 'over-sized', 'ratio' => round($ratio, 2)];
        }

        if ($ratio > 1.35) {
            return ['status' => 'under-sized', 'ratio' => round($ratio, 2)];
        }

        return ['status' => 'valid', 'ratio' => round($ratio, 2)];
    }

    public function mppt(bool $voltageOk, bool $currentOk, int $strings, int $capacity): array
    {
        if (! $voltageOk || ! $currentOk || $strings > $capacity) {
            return ['status' => 'warning'];
        }

        return ['status' => 'valid'];
    }

    public function voltageDrop(float $drop): array
    {
        if ($drop > 5) {
            return ['status' => 'danger'];
        }

        if ($drop > 3) {
            return ['status' => 'warning'];
        }

        return ['status' => 'good'];
    }

    public function protection(float $current, float $breaker, float $fuse, float $spd): array
    {
        if ($breaker < $current || $fuse < $current || $spd < $current) {
            return ['status' => 'danger'];
        }

        return ['status' => 'safe'];
    }

    public function global(array $checks): array
    {
        $statuses = array_map(fn (array $check) => $check['status'] ?? 'valid', $checks);

        if (in_array('danger', $statuses, true) || in_array('insufficient', $statuses, true)) {
            return ['status' => 'rejected'];
        }

        if (in_array('warning', $statuses, true) || in_array('under-sized', $statuses, true) || in_array('over-sized', $statuses, true)) {
            return ['status' => 'review'];
        }

        return ['status' => 'approved'];
    }
}
