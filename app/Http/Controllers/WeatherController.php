<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherService;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function welcome(Request $request)
    {
        $city = $request->input('city', 'Istanbul');
        $weather = $this->weatherService->getWeatherByCity($city);
        
        $error = null;
        $forecast = null;
        
        if (isset($weather['error'])) {
            $error = $weather['error'];
            $weather = null;
        } else {
            $forecast = $this->weatherService->getForecastByCity($city);
        }
        
        return view('welcome', compact('weather', 'forecast', 'error'));
    }

    public function index(Request $request)
    {
        $city = $request->input('city');
        $error = null;
        $weather = null;
        $forecast = null;
        
        $searchCity = !empty($city) ? $city : 'Istanbul';

        $weather = $this->weatherService->getWeatherByCity($searchCity);
        
        if (isset($weather['error'])) {
            $error = $weather['error'];
            $weather = null;
        } else {
            $forecast = $this->weatherService->getForecastByCity($searchCity);
            
            // Save to recent searches session only if it was an actual search
            if (!empty($city)) {
                $recentSearches = session()->get('recent_searches', []);
                if (!in_array($weather['name'], $recentSearches)) {
                    array_unshift($recentSearches, $weather['name']);
                    $recentSearches = array_slice($recentSearches, 0, 5); // Keep last 5
                    session()->put('recent_searches', $recentSearches);
                }
            }
        }

        $recentSearches = session()->get('recent_searches', []);
        
        $greeting = '';
        $favorites = [];
        $isFavorite = false;

        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            
            // Generate Greeting
            $hour = date('H');
            $timeGreeting = 'Good Evening';
            if ($hour >= 5 && $hour < 12) $timeGreeting = 'Good Morning';
            elseif ($hour >= 12 && $hour < 17) $timeGreeting = 'Good Afternoon';
            
            $greeting = $timeGreeting . ', ' . explode(' ', $user->name)[0];

            // Fetch favorites with current weather (simulated via service but we'd normally just pass the names and let the frontend load them, or fetch them here)
            // To be efficient, we just fetch the weather for each favorite
            $favoriteRecords = $user->favoriteCities()->get();
            foreach ($favoriteRecords as $fav) {
                $favWeather = $this->weatherService->getWeatherByCity($fav->city_name);
                if (!isset($favWeather['error'])) {
                    $favorites[] = $favWeather;
                }
                if ($weather && strtolower($fav->city_name) == strtolower($weather['name'])) {
                    $isFavorite = true;
                }
            }
        }

        return view('weather.index', compact('weather', 'forecast', 'city', 'error', 'recentSearches', 'greeting', 'favorites', 'isFavorite'));
    }
}
