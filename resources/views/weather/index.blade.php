@extends('layout.app')

@section('content')

@php
    $bgClass = 'bg-gradient-default';
    
    if(isset($weather) && !isset($error)) {
        $condition = strtolower($weather['weather'][0]['main']);
        $isNight = false;
        
        if(isset($weather['sys']['sunset']) && isset($weather['dt'])) {
            $isNight = $weather['dt'] > $weather['sys']['sunset'] || $weather['dt'] < $weather['sys']['sunrise'];
        }
        
        if($isNight) {
            $bgClass = 'bg-gradient-night';
        } else {
            switch($condition) {
                case 'clear': $bgClass = 'bg-gradient-clear'; break;
                case 'clouds': $bgClass = 'bg-gradient-clouds'; break;
                case 'rain': case 'drizzle': case 'thunderstorm': $bgClass = 'bg-gradient-rain'; break;
                case 'snow': $bgClass = 'bg-gradient-snow'; break;
                default: $bgClass = 'bg-gradient-default';
            }
        }
    }
@endphp

<!-- Dynamic Background Layer -->
<div class="fixed inset-0 z-0 {{ $bgClass }} transition-colors duration-1000 page-bg">
    <!-- Ambient mesh blobs for depth -->
    <div class="mesh-bg">
        <div class="mesh-blob blob-1"></div>
        <div class="mesh-blob blob-2"></div>
    </div>
</div>

<div class="relative z-10 min-h-screen flex flex-col p-4 md:p-8 xl:p-12">
    
    <!-- Top Section: Header & Search -->
    <header class="w-full max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6 mb-10">
        
        <div class="flex flex-col gap-1">
            <a href="{{ route('weather.index') }}" class="flex items-center gap-4 hover:opacity-80 transition-opacity">
                <div class="w-12 h-12 rounded-2xl glass-panel flex items-center justify-center text-white">
                    <i class="fa-solid fa-cloud-sun text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">SkyCast</h1>
                    <p class="text-xs font-medium text-white/60 tracking-wider uppercase flex items-center">
                        {{ date('l, F j') }}
                        <span class="opacity-50 mx-1.5">•</span> 
                        <span id="live-clock"></span>
                    </p>
                </div>
            </a>
            @auth
                <div class="text-sm font-medium text-blue-300 tracking-wide mt-1 animate-pulse">
                    {{ $greeting }} <i class="fa-solid fa-hand-sparkles text-xs ml-1"></i>
                </div>
            @endauth
        </div>

        <!-- Search & Auth -->
        <div class="flex flex-row items-center gap-3">
            <form action="{{ route('weather.index') }}" method="GET" class="flex-1 md:w-80 md:flex-none relative flex items-center">
                <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-white/50"></i>
                <input 
                    type="text" 
                    name="city" 
                    value="{{ request('city', '') }}"
                    placeholder="Search location..." 
                    autocomplete="off"
                    class="w-full h-12 pl-11 pr-14 bg-white/10 border border-white/20 rounded-full text-white placeholder-white/50 focus:outline-none focus:bg-white/20 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all shadow-inner backdrop-blur-md search-input"
                >
                <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/30 transition-all border border-white/10 text-white/80 hover:text-white group">
                    <i class="fa-solid fa-magnifying-glass text-sm group-hover:scale-110 transition-transform"></i>
                </button>
            </form>
            
            <div class="flex items-center gap-2 shrink-0">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-10 h-10 rounded-full glass-panel flex items-center justify-center text-white/70 hover:text-red-400 hover:bg-red-500/20 transition-all" title="Logout">
                            <i class="fa-solid fa-power-off"></i>
                        </button>
                    </form>
                @else
                    <div class="flex items-center gap-2 sm:gap-4">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-white/80 hover:text-white transition-colors hidden sm:block">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="glass-pill px-3 py-2 sm:px-5 text-sm font-bold text-white hover:bg-white/20 transition-colors bg-white/10 whitespace-nowrap">
                            <span class="hidden sm:inline">Create Account</span>
                            <span class="sm:hidden">Register</span>
                        </a>
                    </div>
                @endauth
            </div>
        </div>

    </header>

    <!-- Favorites Row (Floating) -->
    @if(isset($favorites) && count($favorites) > 0)
    <div class="w-full max-w-7xl mx-auto mb-8">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-white/60 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-star text-yellow-500"></i> Saved Intelligence
        </h3>
        <div class="flex gap-4 overflow-x-auto pb-2 snap-x no-scrollbar">
            @foreach($favorites as $fav)
            <a href="{{ route('weather.index', ['city' => $fav['name']]) }}" class="snap-start shrink-0 flex items-center gap-3 p-3 glass-panel hover:bg-white/10 transition-all w-48 group">
                <img src="http://openweathermap.org/img/wn/{{ $fav['weather'][0]['icon'] }}.png" alt="Icon" class="w-10 h-10 drop-shadow-md group-hover:scale-110 transition-transform">
                <div>
                    <p class="text-sm font-bold text-white leading-tight">{{ $fav['name'] }}</p>
                    <p class="text-xs font-medium text-blue-200">{{ round($fav['main']['temp']) }}&deg; • <span class="capitalize">{{ $fav['weather'][0]['description'] }}</span></p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Main Dashboard -->
    <main class="w-full max-w-7xl mx-auto flex-grow">
        @if(isset($error))
            <div class="glass-panel p-8 text-center max-w-lg mx-auto mt-20">
                <i class="fa-solid fa-triangle-exclamation text-4xl text-red-300 mb-4 opacity-80"></i>
                <h3 class="text-xl font-semibold mb-2">{{ $error }}</h3>
                <p class="text-sm text-white/70">The location you searched for could not be found. Please try another query.</p>
            </div>
        @elseif(isset($weather))
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
                
                <!-- LEFT COLUMN: Primary Weather -->
                <div class="lg:col-span-5 flex flex-col gap-6">
                    
                    <!-- Main Weather Card -->
                    <div class="glass-panel p-5 sm:p-8 flex flex-col justify-between relative overflow-hidden" style="min-height: 340px;">
                        <!-- Decorative blurred circle in corner -->
                        <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>

                        <div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <h2 class="text-4xl font-semibold tracking-tight">{{ $weather['name'] }}</h2>
                                        @auth
                                        <form method="POST" action="{{ route('favorites.toggle') }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="city_name" value="{{ $weather['name'] }}">
                                            <button type="submit" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-white/10 transition-colors" title="{{ $isFavorite ? 'Remove from favorites' : 'Add to favorites' }}">
                                                <i class="fa-{{ $isFavorite ? 'solid' : 'regular' }} fa-star text-yellow-400 text-lg hover:scale-110 transition-transform"></i>
                                            </button>
                                        </form>
                                        @endauth
                                    </div>
                                    <p class="text-lg text-white/70 mt-1 font-medium">{{ $weather['sys']['country'] ?? '' }}</p>
                                </div>
                                <div class="glass-pill px-4 py-1.5 text-sm font-medium flex items-center gap-2">
                                    <i class="fa-solid fa-location-arrow text-xs text-white/70"></i> Live
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between mt-auto">
                            <div>
                                <div class="text-6xl sm:text-8xl font-light tracking-tighter">{{ round($weather['main']['temp']) }}&deg;</div>
                                <p class="text-2xl font-medium mt-2 capitalize opacity-90">{{ $weather['weather'][0]['description'] }}</p>
                                <p class="text-sm text-white/70 mt-1">H:{{ round($weather['main']['temp_max']) }}&deg; L:{{ round($weather['main']['temp_min']) }}&deg;</p>
                            </div>
                            <img src="http://openweathermap.org/img/wn/{{ $weather['weather'][0]['icon'] }}@4x.png" alt="Icon" class="w-28 h-28 sm:w-40 sm:h-40 md:w-48 md:h-48 animated-icon -mr-3 sm:-mr-6">
                        </div>
                    </div>

                    <!-- Highlight Metrics Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="glass-panel p-5 flex flex-col">
                            <div class="flex items-center gap-2 text-white/60 text-sm font-medium uppercase tracking-wider mb-3">
                                <i class="fa-solid fa-temperature-half"></i> Feels Like
                            </div>
                            <div class="text-3xl font-semibold">{{ round($weather['main']['feels_like']) }}&deg;</div>
                            <div class="text-xs text-white/60 mt-auto pt-2">Similar to actual</div>
                        </div>
                        
                        <div class="glass-panel p-5 flex flex-col">
                            <div class="flex items-center gap-2 text-white/60 text-sm font-medium uppercase tracking-wider mb-3">
                                <i class="fa-solid fa-droplet"></i> Humidity
                            </div>
                            <div class="text-3xl font-semibold">{{ $weather['main']['humidity'] }}%</div>
                            <div class="text-xs text-white/60 mt-auto pt-2">Dew point {{ round($weather['main']['temp'] - ((100 - $weather['main']['humidity'])/5)) }}&deg;</div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Hourly & Extra Info -->
                <div class="lg:col-span-7 flex flex-col gap-6">
                    
                    <!-- Hourly / Step Forecast -->
                    @if(isset($forecast) && isset($forecast['list']))
                    <div class="glass-panel p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-sm font-semibold uppercase tracking-wider text-white/80 flex items-center gap-2">
                                <i class="fa-regular fa-clock"></i> 24-Hour Forecast
                            </h3>
                        </div>
                        
                        <div class="flex gap-4 overflow-x-auto pb-2 snap-x no-scrollbar">
                            @php
                                // Take first 8 items (approx 24 hours of 3-hour steps)
                                $hourlyForecasts = array_slice($forecast['list'], 0, 8);
                            @endphp
                            
                            @foreach($hourlyForecasts as $f)
                            <div class="snap-start shrink-0 flex flex-col items-center justify-between p-3 rounded-2xl hover:bg-white/5 transition w-20">
                                <p class="text-sm font-medium text-white/90">{{ date('H:i', $f['dt']) }}</p>
                                <img src="http://openweathermap.org/img/wn/{{ $f['weather'][0]['icon'] }}.png" alt="Icon" class="w-12 h-12 my-2 drop-shadow-md">
                                <p class="text-lg font-bold">{{ round($f['main']['temp']) }}&deg;</p>
                                <p class="text-xs text-blue-200 mt-1 font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-umbrella text-[10px]"></i> {{ isset($f['pop']) ? round($f['pop'] * 100) : 0 }}%
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Secondary Metrics Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="glass-panel p-5">
                            <h4 class="text-xs text-white/60 font-medium uppercase tracking-wider mb-2 flex items-center gap-1.5"><i class="fa-solid fa-wind"></i> Wind</h4>
                            <div class="text-xl font-semibold">{{ round($weather['wind']['speed'] * 3.6) }} <span class="text-sm font-normal text-white/70">km/h</span></div>
                        </div>
                        
                        <div class="glass-panel p-5">
                            <h4 class="text-xs text-white/60 font-medium uppercase tracking-wider mb-2 flex items-center gap-1.5"><i class="fa-solid fa-eye"></i> Visibility</h4>
                            <div class="text-xl font-semibold">{{ isset($weather['visibility']) ? round($weather['visibility'] / 1000) : '--' }} <span class="text-sm font-normal text-white/70">km</span></div>
                        </div>

                        <div class="glass-panel p-5">
                            <h4 class="text-xs text-white/60 font-medium uppercase tracking-wider mb-2 flex items-center gap-1.5"><i class="fa-solid fa-gauge"></i> Pressure</h4>
                            <div class="text-xl font-semibold">{{ $weather['main']['pressure'] }} <span class="text-sm font-normal text-white/70">hPa</span></div>
                        </div>
                        
                        <div class="glass-panel p-5">
                            <h4 class="text-xs text-white/60 font-medium uppercase tracking-wider mb-2 flex items-center gap-1.5"><i class="fa-solid fa-cloud"></i> Clouds</h4>
                            <div class="text-xl font-semibold">{{ $weather['clouds']['all'] ?? 0 }}<span class="text-sm font-normal text-white/70">%</span></div>
                        </div>
                    </div>

                    <!-- Sun & Moon Card -->
                    <div class="glass-panel p-6 flex flex-col justify-center">
                        <div class="flex justify-between items-center w-full">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-sunrise text-3xl text-yellow-300 mb-2 drop-shadow-md"></i>
                                <span class="text-xs text-white/60 uppercase tracking-wider font-medium">Sunrise</span>
                                <span class="text-lg font-semibold mt-1">{{ date('H:i', $weather['sys']['sunrise'] + ($weather['timezone'] ?? 0) - date('Z')) }}</span>
                            </div>
                            
                            <!-- Sun arc visual -->
                            <div class="flex-grow mx-8 relative h-12">
                                <div class="absolute inset-0 border-t-2 border-dashed border-white/30 rounded-t-full w-full h-24"></div>
                                <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 w-4 h-4 bg-yellow-400 rounded-full shadow-[0_0_15px_rgba(250,204,21,0.8)]"></div>
                            </div>
                            
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-sunset text-3xl text-orange-400 mb-2 drop-shadow-md"></i>
                                <span class="text-xs text-white/60 uppercase tracking-wider font-medium">Sunset</span>
                                <span class="text-lg font-semibold mt-1">{{ date('H:i', $weather['sys']['sunset'] + ($weather['timezone'] ?? 0) - date('Z')) }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- BOTTOM: 5-Day Extended Forecast -->
            @if(isset($forecast) && isset($forecast['list']))
            <div class="mt-6 glass-panel p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-white/80 flex items-center gap-2 mb-6">
                    <i class="fa-solid fa-calendar-days"></i> Extended Forecast
                </h3>
                
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    @php
                        $dailyForecasts = [];
                        foreach($forecast['list'] as $f) {
                            $date = date('Y-m-d', $f['dt']);
                            if(!isset($dailyForecasts[$date]) && count($dailyForecasts) < 5) {
                                // Prefer noon forecast for the daily representation
                                if (date('H', $f['dt']) >= 12 || empty($dailyForecasts[$date])) {
                                    $dailyForecasts[$date] = $f;
                                }
                            }
                        }
                    @endphp

                    @foreach($dailyForecasts as $date => $f)
                    <div class="flex flex-col items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5 hover:bg-white/10 transition-colors">
                        <p class="text-sm font-medium uppercase">{{ date('D', $f['dt']) }}</p>
                        <p class="text-xs text-white/50">{{ date('M j', $f['dt']) }}</p>
                        <img src="http://openweathermap.org/img/wn/{{ $f['weather'][0]['icon'] }}@2x.png" alt="Icon" class="w-16 h-16 my-2 filter drop-shadow-lg">
                        <div class="flex items-end gap-2">
                            <span class="text-xl font-bold">{{ round($f['main']['temp']) }}&deg;</span>
                        </div>
                        <p class="text-xs font-medium text-white/60 capitalize mt-2 text-center">{{ $f['weather'][0]['description'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        @else
            <!-- Initial State -->
            <div class="glass-panel p-16 text-center max-w-2xl mx-auto mt-20 flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-white/10 rounded-3xl flex items-center justify-center mb-6 shadow-inner backdrop-blur-xl">
                    <i class="fa-solid fa-location-crosshairs text-4xl text-white/80 drop-shadow-md"></i>
                </div>
                <h2 class="text-3xl font-bold mb-3 tracking-tight">Weather Intelligence</h2>
                <p class="text-white/60 mb-8 max-w-md mx-auto leading-relaxed">Search for any global location to view real-time meteorological data in a beautiful, premium dashboard.</p>
                
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('weather.index', ['city' => 'San Francisco']) }}" class="glass-pill px-5 py-2 text-sm font-medium">San Francisco</a>
                    <a href="{{ route('weather.index', ['city' => 'London']) }}" class="glass-pill px-5 py-2 text-sm font-medium">London</a>
                    <a href="{{ route('weather.index', ['city' => 'Tokyo']) }}" class="glass-pill px-5 py-2 text-sm font-medium">Tokyo</a>
                </div>
            </div>
        @endif
        
    </main>

    <footer class="mt-12 text-center pb-4 text-xs font-medium uppercase tracking-widest text-white/40">
        &copy; {{ date('Y') }} SkyCast. All Rights Reserved.
    </footer>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const clockElement = document.getElementById('live-clock');
        if (clockElement) {
            clockElement.innerText = hours + ':' + minutes;
        }
    }
    
    // Call immediately to prevent empty text or flashing
    updateClock();
    
    // Update every second so the minute rolls over precisely
    setInterval(updateClock, 1000);
</script>
@endsection
