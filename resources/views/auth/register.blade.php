@extends('layout.app')

@section('content')

@php
    $bgImage = 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1600&auto=format&fit=crop';
@endphp

<!-- Dynamic Background Layer with Dark Overlay -->
<div class="fixed inset-0 z-0 bg-cover bg-center transition-all duration-1000" style="background-image: url('{{ $bgImage }}');"></div>
<div class="fixed inset-0 z-0 bg-slate-900 bg-opacity-75 backdrop-filter backdrop-blur-sm"></div>

<div class="relative z-10 min-h-screen px-4 py-8 flex items-center justify-center">
    
    <div class="glass-panel p-7 sm:p-10 w-full max-w-md flex flex-col relative overflow-hidden my-6">
        <!-- Decorative blurred circle in corner -->
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex justify-center mb-8 relative z-10">
            <a href="{{ route('weather.index') }}" class="flex items-center gap-4 hover:opacity-80 transition-opacity">
                <div class="w-14 h-14 rounded-2xl glass-panel flex items-center justify-center text-white">
                    <i class="fa-solid fa-cloud-sun text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white">SkyCast</h1>
                </div>
            </a>
        </div>
        
        <h2 class="text-sm text-center text-white/60 font-semibold uppercase tracking-wider mb-8 relative z-10">Create Dashboard Account</h2>

        <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-5 relative z-10">
            @csrf

            @if($errors->any())
                <div class="glass-panel border-red-500/50 bg-red-500/10 text-red-200 text-sm p-4 rounded-xl flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-2" for="name">Your Name</label>
                <input class="w-full h-12 px-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:bg-white/20 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all shadow-inner backdrop-blur-md" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
            </div>

            <div>
                <label class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-2" for="email">Email Address</label>
                <input class="w-full h-12 px-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:bg-white/20 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all shadow-inner backdrop-blur-md" id="email" type="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-2" for="password">Password</label>
                <input class="w-full h-12 px-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:bg-white/20 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all shadow-inner backdrop-blur-md" id="password" type="password" name="password" required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-2" for="password_confirmation">Confirm Password</label>
                <input class="w-full h-12 px-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:bg-white/20 focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all shadow-inner backdrop-blur-md" id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            <button type="submit" class="w-full h-12 mt-4 glass-panel bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl transition-all border border-white/20 flex items-center justify-center gap-2 group">
                Create Account <i class="fa-solid fa-check text-sm group-hover:scale-110 transition-transform"></i>
            </button>
            
            <p class="text-center text-sm text-white/60 mt-4">
                Already have an account? <a href="{{ route('login') }}" class="text-white hover:text-blue-300 transition-colors font-medium underline underline-offset-4">Sign In</a>
            </p>
        </form>
    </div>
</div>
@endsection
