@extends('layout.app')

@section('content')

@php
    $bgClass = 'bg-gradient-clouds';
@endphp

<!-- Dynamic Background Layer -->
<div class="fixed inset-0 z-0 {{ $bgClass }} transition-colors duration-1000 page-bg">
    <div class="mesh-bg">
        <div class="mesh-blob blob-1"></div>
        <div class="mesh-blob blob-2"></div>
    </div>
</div>

<div class="relative z-10 min-h-screen flex flex-col p-4 sm:p-6 md:p-8 xl:p-12">
    
    <!-- Header -->
    <header class="w-full max-w-7xl mx-auto flex justify-between items-center mb-10 md:mb-16">
        
        <a href="{{ route('welcome') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
            <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl glass-panel flex items-center justify-center text-white shrink-0">
                <i class="fa-solid fa-cloud-sun text-xl md:text-2xl"></i>
            </div>
            <div>
                <h1 class="text-lg md:text-xl font-bold tracking-tight">SkyCast</h1>
                <p class="text-xs font-medium text-white/60 tracking-wider uppercase hidden sm:block">
                    Weather Intelligence
                </p>
            </div>
        </a>

        <div class="flex items-center gap-2 sm:gap-4">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-white/80 hover:text-white transition-colors">
                Sign In
            </a>
            <a href="{{ route('register') }}" class="glass-pill px-3 py-2 sm:px-5 sm:py-2 text-sm font-bold text-white hover:bg-white/20 transition-colors bg-white/10 border border-white/20 whitespace-nowrap">
                <span class="hidden sm:inline">Create Account</span>
                <span class="sm:hidden">Register</span>
            </a>
        </div>
    </header>

    <!-- Main -->
    <main class="w-full max-w-7xl mx-auto flex-grow flex flex-col justify-center gap-6 md:gap-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
            
            <!-- Hero Card -->
            <div class="lg:col-span-7">
                <div class="glass-panel p-7 sm:p-10 md:p-12 flex flex-col justify-center relative overflow-hidden">
                    <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
                    
                    <div class="glass-pill inline-flex items-center gap-2 px-3 py-1.5 text-xs sm:text-sm font-medium w-max mb-4 md:mb-6">
                        <i class="fa-solid fa-bolt text-yellow-300"></i> Next-Gen Forecasts
                    </div>

                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold tracking-tight mb-4 md:mb-6 leading-tight">
                        Experience the weather like never before.
                    </h2>
                    
                    <p class="text-base md:text-lg text-white/70 font-medium mb-7 md:mb-10 max-w-lg leading-relaxed">
                        SkyCast delivers hyper-accurate meteorological data through a breathtaking, distraction-free interface. Sign in to access your personal weather dashboard.
                    </p>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <a href="{{ route('register') }}" class="glass-pill px-6 py-3 sm:px-8 sm:py-4 text-sm sm:text-base font-bold text-white hover:bg-white/20 transition-colors bg-white/10 border border-white/20 flex items-center gap-3">
                            Get Started <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <a href="{{ route('login') }}" class="text-sm text-white/70 hover:text-white transition-colors font-medium">
                            Already have an account? <span class="underline underline-offset-4">Sign In</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Feature Cards -->
            <div class="lg:col-span-5 grid grid-cols-2 gap-3 sm:gap-4">
                
                <div class="glass-panel p-4 sm:p-6 flex flex-col">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/10 flex items-center justify-center mb-3 sm:mb-4">
                        <i class="fa-solid fa-temperature-half text-white/80 text-sm sm:text-base"></i>
                    </div>
                    <h3 class="text-sm sm:text-base font-semibold mb-1">Real-Time Data</h3>
                    <p class="text-xs sm:text-sm text-white/60 leading-relaxed">Instant temperature, humidity & pressure updates.</p>
                </div>
                
                <div class="glass-panel p-4 sm:p-6 flex flex-col">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/10 flex items-center justify-center mb-3 sm:mb-4">
                        <i class="fa-solid fa-clock text-white/80 text-sm sm:text-base"></i>
                    </div>
                    <h3 class="text-sm sm:text-base font-semibold mb-1">24h Forecast</h3>
                    <p class="text-xs sm:text-sm text-white/60 leading-relaxed">Hourly predictions to plan your day perfectly.</p>
                </div>
                
                <div class="glass-panel p-4 sm:p-6 flex flex-col">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/10 flex items-center justify-center mb-3 sm:mb-4">
                        <i class="fa-solid fa-calendar-days text-white/80 text-sm sm:text-base"></i>
                    </div>
                    <h3 class="text-sm sm:text-base font-semibold mb-1">5-Day Outlook</h3>
                    <p class="text-xs sm:text-sm text-white/60 leading-relaxed">Comprehensive extended weather analysis.</p>
                </div>
                
                <div class="glass-panel p-4 sm:p-6 flex flex-col">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/10 flex items-center justify-center mb-3 sm:mb-4">
                        <i class="fa-solid fa-star text-white/80 text-sm sm:text-base"></i>
                    </div>
                    <h3 class="text-sm sm:text-base font-semibold mb-1">Saved Cities</h3>
                    <p class="text-xs sm:text-sm text-white/60 leading-relaxed">Switch between favourite locations instantly.</p>
                </div>

            </div>
        </div>

    </main>

    <footer class="mt-8 md:mt-12 text-center pb-4 text-xs font-medium uppercase tracking-widest text-white/40">
        &copy; {{ date('Y') }} SkyCast. All Rights Reserved.
    </footer>
</div>

@endsection
