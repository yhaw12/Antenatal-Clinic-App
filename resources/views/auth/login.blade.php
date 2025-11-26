@extends('layouts.app')

@section('title','Login — ANC Clinic')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center p-4">
    
    {{-- Main Card Container --}}
    <div class="card w-full max-w-4xl overflow-hidden shadow-2xl">
        
        {{-- Brand Top Border --}}
        <div class="h-1.5" style="background: var(--brand)"></div>

        <div class="md:flex">
            
            {{-- LEFT: Welcome/Banner --}}
            <section class="md:w-7/12 p-8 flex flex-col justify-center relative overflow-hidden">
                <div class="absolute inset-0 pointer-events-none opacity-10 dark:opacity-5" 
                     style="background: radial-gradient(circle at top left, var(--brand), transparent);">
                </div>

                <div class="relative z-10">
                    <h2 class="text-2xl font-bold text-body mb-3">Welcome to ANC Clinic</h2>
                    <p class="text-sm text-muted mb-8 leading-relaxed">
                        Secure access for health professionals. Manage patient records, appointments, and vitals efficiently.
                    </p>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="rounded-xl overflow-hidden border border-border bg-surface/50 shadow-sm p-2">
                            <img src="{{ asset('images/anc-banner-1.svg') }}" alt="Maternal care" class="w-full h-32 object-cover rounded-lg opacity-90 hover:opacity-100 transition-opacity">
                        </div>
                        <div class="rounded-xl overflow-hidden border border-border bg-surface/50 shadow-sm p-2">
                            <img src="{{ asset('images/anc-banner-2.svg') }}" alt="Clinic care" class="w-full h-32 object-cover rounded-lg opacity-90 hover:opacity-100 transition-opacity">
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-border">
                        <p class="text-xs text-muted flex items-center gap-2">
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Support: admin@{{ request()->getHost() }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- RIGHT: Login Panel --}}
            <aside class="md:w-5/12 p-8 border-t md:border-t-0 md:border-l border-border flex flex-col justify-center" 
                   style="background-color: color-mix(in srgb, var(--surface) 50%, var(--bg));">
                
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-body">Sign In</h3>
                    <p class="text-xs text-muted mt-1">Enter your credentials to continue.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1.5">Email Address</label>
                        <input name="email" type="email" value="{{ old('email') }}" required autofocus
                               class="w-full rounded-lg border border-border bg-surface text-body px-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all placeholder-muted/50" 
                               placeholder="doctor@clinic.com" />
                        @error('email') <div class="text-xs text-danger mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1.5">Password</label>
                        <input name="password" type="password" required
                               class="w-full rounded-lg border border-border bg-surface text-body px-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all" 
                               placeholder="••••••••" />
                        @error('password') <div class="text-xs text-danger mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-border text-brand focus:ring-brand bg-surface" /> 
                            <span class="text-sm text-muted">Remember me</span>
                        </label>
                        
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium" style="color: var(--brand)">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center py-3 text-sm font-bold shadow-lg shadow-brand/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Secure Access
                    </button>

                </form>

                {{-- Footer / Registration Link --}}
                @if(env('APP_ALLOW_TEMP_REGISTER', false))
                    <div class="mt-8 pt-6 border-t border-border text-center">
                        <p class="text-xs text-muted mb-3">No account yet?</p>
                        <a href="{{ route('temp.register') }}" class="btn-ghost w-full justify-center text-xs">
                            Create Temporary Account
                        </a>
                    </div>
                @endif

                <div class="mt-6 text-center">
                    <p class="text-[10px] text-muted/60">
                        Authorized personnel only. <br> Patient data privacy laws apply.
                    </p>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection