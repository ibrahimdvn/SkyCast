<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    public function getWeatherByCity(string $city)
    {
        $cacheKey = "weather_" . strtolower(str_replace(' ', '_', $city));
        
        return Cache::remember($cacheKey, 300, function () use ($city) {
            // 1. Geocoding: Get lat/lon for the city
            $geoResponse = Http::get("https://geocoding-api.open-meteo.com/v1/search", [
                'name' => $city,
                'count' => 1,
                'language' => 'en',
                'format' => 'json'
            ]);

            if ($geoResponse->failed() || empty($geoResponse->json('results'))) {
                return ['error' => 'City not found.'];
            }

            $location = $geoResponse->json('results')[0];
            $lat = $location['latitude'];
            $lon = $location['longitude'];

            // 2. Fetch Weather Data from Open-Meteo
            $weatherResponse = Http::get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $lat,
                'longitude' => $lon,
                'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,is_day,precipitation,weather_code,cloud_cover,pressure_msl,surface_pressure,wind_speed_10m,visibility',
                'hourly' => 'temperature_2m,precipitation_probability,weather_code',
                'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,sunrise,sunset',
                'timezone' => 'auto',
                'forecast_days' => 6
            ]);

            if ($weatherResponse->failed()) {
                return ['error' => 'Could not fetch weather data.'];
            }

            $data = $weatherResponse->json();
            
            // 3. Format data to match our existing view structure (which expects OpenWeatherMap format)
            // Or adapt it. Since the view expects OpenWeatherMap structure, I will map Open-Meteo to OpenWeatherMap structure!
            
            $current = $data['current'];
            $daily = $data['daily'];
            $hourly = $data['hourly'];
            
            // Map Open-Meteo WMO weather codes to OpenWeatherMap main & icons
            $wmoMap = $this->mapWmoCode($current['weather_code'], $current['is_day']);

            $formattedWeather = [
                'name' => $location['name'],
                'sys' => [
                    'country' => $location['country'] ?? '',
                    'sunrise' => strtotime($daily['sunrise'][0]),
                    'sunset' => strtotime($daily['sunset'][0]),
                ],
                'dt' => strtotime($current['time']),
                'timezone' => 0, // Using auto timezone from API
                'main' => [
                    'temp' => $current['temperature_2m'],
                    'feels_like' => $current['apparent_temperature'],
                    'temp_min' => $daily['temperature_2m_min'][0],
                    'temp_max' => $daily['temperature_2m_max'][0],
                    'pressure' => $current['pressure_msl'],
                    'humidity' => $current['relative_humidity_2m'],
                ],
                'weather' => [
                    [
                        'main' => $wmoMap['main'],
                        'description' => $wmoMap['description'],
                        'icon' => $wmoMap['icon']
                    ]
                ],
                'wind' => [
                    'speed' => $current['wind_speed_10m'] / 3.6, // Open-Meteo returns km/h, OWM is m/s, but our view * 3.6 to get km/h. So we divide by 3.6 to keep view logic working!
                ],
                'clouds' => [
                    'all' => $current['cloud_cover']
                ],
                'visibility' => $current['visibility'] ?? 10000
            ];

            return $formattedWeather;
        });
    }

    public function getForecastByCity(string $city)
    {
        $cacheKey = "forecast_" . strtolower(str_replace(' ', '_', $city));
        
        return Cache::remember($cacheKey, 300, function () use ($city) {
            // Already cached from geocoding above usually, but we fetch again or we can just fetch once.
            // Since we need it in standard OWM format for the view:
            
            $geoResponse = Http::get("https://geocoding-api.open-meteo.com/v1/search", [
                'name' => $city,
                'count' => 1
            ]);
            
            if ($geoResponse->failed() || empty($geoResponse->json('results'))) {
                return null;
            }
            
            $location = $geoResponse->json('results')[0];
            
            $weatherResponse = Http::get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'hourly' => 'temperature_2m,precipitation_probability,weather_code',
                'daily' => 'weather_code,temperature_2m_max,temperature_2m_min',
                'timezone' => 'auto',
                'forecast_days' => 6
            ]);
            
            $data = $weatherResponse->json();
            $hourlyList = [];
            
            // Find current time index
            $currentTime = time();
            $startIndex = 0;
            foreach ($data['hourly']['time'] as $index => $timeStr) {
                if (strtotime($timeStr) > $currentTime) {
                    $startIndex = $index;
                    break;
                }
            }
            
            // Get next 8 hours (simulate 3-hour steps for the UI)
            for ($i = 0; $i < 24; $i += 3) {
                $idx = $startIndex + $i;
                if (!isset($data['hourly']['time'][$idx])) continue;
                
                $isDay = true; // simplifying for hourly
                $wmoMap = $this->mapWmoCode($data['hourly']['weather_code'][$idx], $isDay);
                
                $hourlyList[] = [
                    'dt' => strtotime($data['hourly']['time'][$idx]),
                    'main' => [
                        'temp' => $data['hourly']['temperature_2m'][$idx],
                    ],
                    'weather' => [
                        [
                            'main' => $wmoMap['main'],
                            'description' => $wmoMap['description'],
                            'icon' => $wmoMap['icon']
                        ]
                    ],
                    'pop' => ($data['hourly']['precipitation_probability'][$idx] ?? 0) / 100,
                ];
            }
            
            // For daily (the view expects 5-day forecast in 'list' at 12:00)
            // So we just add daily items to the list too, marking them at noon
            foreach ($data['daily']['time'] as $idx => $timeStr) {
                $isDay = true;
                $wmoMap = $this->mapWmoCode($data['daily']['weather_code'][$idx], $isDay);
                
                $hourlyList[] = [
                    'dt' => strtotime($timeStr . ' 12:00:00'),
                    'main' => [
                        'temp' => $data['daily']['temperature_2m_max'][$idx],
                    ],
                    'weather' => [
                        [
                            'main' => $wmoMap['main'],
                            'description' => $wmoMap['description'],
                            'icon' => $wmoMap['icon']
                        ]
                    ],
                ];
            }

            return ['list' => $hourlyList];
        });
    }

    private function mapWmoCode($code, $isDay)
    {
        $iconSuffix = $isDay ? 'd' : 'n';
        
        // WMO interpretation
        if ($code == 0) {
            return ['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01' . $iconSuffix];
        } elseif ($code == 1) {
            return ['main' => 'Clouds', 'description' => 'mainly clear', 'icon' => '02' . $iconSuffix];
        } elseif ($code == 2) {
            return ['main' => 'Clouds', 'description' => 'partly cloudy', 'icon' => '03' . $iconSuffix];
        } elseif ($code == 3) {
            return ['main' => 'Clouds', 'description' => 'overcast', 'icon' => '04' . $iconSuffix];
        } elseif (in_array($code, [45, 48])) {
            return ['main' => 'Clouds', 'description' => 'fog', 'icon' => '50' . $iconSuffix];
        } elseif (in_array($code, [51, 53, 55, 56, 57])) {
            return ['main' => 'Drizzle', 'description' => 'drizzle', 'icon' => '09' . $iconSuffix];
        } elseif (in_array($code, [61, 63, 65, 66, 67])) {
            return ['main' => 'Rain', 'description' => 'rain', 'icon' => '10' . $iconSuffix];
        } elseif (in_array($code, [71, 73, 75, 77, 85, 86])) {
            return ['main' => 'Snow', 'description' => 'snow', 'icon' => '13' . $iconSuffix];
        } elseif (in_array($code, [80, 81, 82])) {
            return ['main' => 'Rain', 'description' => 'rain showers', 'icon' => '09' . $iconSuffix];
        } elseif (in_array($code, [95, 96, 99])) {
            return ['main' => 'Thunderstorm', 'description' => 'thunderstorm', 'icon' => '11' . $iconSuffix];
        }

        return ['main' => 'Clear', 'description' => 'clear', 'icon' => '01' . $iconSuffix];
    }
}
