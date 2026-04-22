<?php

namespace App\Services\Solar;

use Illuminate\Support\Facades\Http;
use Throwable;

class WeatherService
{
    public function getIrradiation($lat, $lon)
    {
        try {
            $data = Http::timeout(10)->get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $lat,
                'longitude' => $lon,
                'daily' => 'shortwave_radiation_sum',
                'timezone' => 'auto'
            ])->throw()->json();
        } catch (Throwable $exception) {
            return 5.5;
        }

        if (!isset($data['daily']['shortwave_radiation_sum'])) {
            return 5.5;
        }

        $radiation = $data['daily']['shortwave_radiation_sum'];

        $average = array_sum($radiation) / count($radiation);

        return round($average / 1000, 2);
    }

    public function getCoordinatesFromCity($city)
    {
        try {
            $data = Http::timeout(10)->get("https://geocoding-api.open-meteo.com/v1/search", [
                'name' => $city,
                'count' => 1
            ])->throw()->json();
        } catch (Throwable $exception) {
            return [
                'lat' => 33.5731,
                'lon' => -7.5898
            ];
        }

        if (!isset($data['results'][0])) {
            return [
                'lat' => 33.5731,
                'lon' => -7.5898
            ];
        }

        return [
            'lat' => $data['results'][0]['latitude'],
            'lon' => $data['results'][0]['longitude']
        ];
    }
}
