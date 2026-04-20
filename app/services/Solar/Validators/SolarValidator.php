<?php

namespace App\Services\Solar\Validators;

class SolarValidator
{
    public function inverter($pv, $inverter)
    {
        $ratio = $inverter / $pv;

        if ($ratio < 0.8) {
            return ['status' => 'under-sized'];
        }

        if ($ratio > 1.2) {
            return ['status' => 'over-sized'];
        }

        return ['status' => 'valid'];
    }

    public function voltageDrop($drop)
    {
        if ($drop > 5) return ['status' => 'danger'];
        if ($drop > 3) return ['status' => 'warning'];

        return ['status' => 'good'];
    }

    public function protection($current, $breaker, $fuse)
    {
        if ($breaker < $current || $fuse < $current) {
            return ['status' => 'danger'];
        }

        return ['status' => 'safe'];
    }

    public function global($pv, $consumption, $inverter, $voltage, $protection)
    {
        if ($pv < $consumption) {
            return ['status' => 'under-sized'];
        }

        if (
            $inverter['status'] !== 'valid' ||
            $voltage['status'] === 'danger' ||
            $protection['status'] === 'danger'
        ) {
            return ['status' => 'rejected'];
        }

        return ['status' => 'approved'];
    }
}