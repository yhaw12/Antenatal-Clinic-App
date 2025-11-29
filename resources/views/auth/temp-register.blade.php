@extends('layouts.app')

@section('title','Temporary Registration')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center p-4">

    {{-- Main Card --}}
    <div class="card w-full max-w-lg overflow-hidden shadow-2xl">
        
        {{-- Brand Top Border --}}
        <div class="h-1.5" style="background: var(--brand)"></div>

        <div class="p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-body">Temporary Registration</h1>
                <p class="text-sm text-muted mt-2">
                    Demo access only. Please provide your token.
                </p>
            </div>

            <form method="POST" action="{{ route('temp.register.post') }}" class="space-y-5">
                @csrf

                {{-- Name --}}
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1.5">Full Name</label>
                    <input name="name" value="{{ old('name') }}" required autofocus
                           class="w-full rounded-lg border border-border bg-surface text-body px-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all placeholder-muted/50" />
                    @error('name') <div class="text-xs text-danger mt-1 font-medium">{{ $message }}</div> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1.5">Email Address</label>
                    <input name="email" value="{{ old('email') }}" required
                           class="w-full rounded-lg border border-border bg-surface text-body px-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all placeholder-muted/50" />
                    @error('email') <div class="text-xs text-danger mt-1 font-medium">{{ $message }}</div> @enderror
                </div>

                {{-- Password Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1.5">Password</label>
                        <input name="password" type="password" required
                               class="w-full rounded-lg border border-border bg-surface text-body px-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all" />
                        @error('password') <div class="text-xs text-danger mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1.5">Confirm</label>
                        <input name="password_confirmation" type="password" required
                               class="w-full rounded-lg border border-border bg-surface text-body px-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all" />
                    </div>
                </div>

                {{-- Token --}}
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-1.5">Registration Token</label>
                    <input name="token" value="{{ old('token') }}" required
                           class="w-full rounded-lg border border-border bg-surface text-body px-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all placeholder-muted/50" 
                           placeholder="Enter secure token" />
                    @error('token') <div class="text-xs text-danger mt-1 font-medium">{{ $message }}</div> @enderror
                </div>

                {{-- Actions --}}
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <a href="{{ route('login') }}" class="btn-ghost w-full justify-center text-center py-2.5">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary w-full justify-center py-2.5 shadow-lg shadow-brand/20">
                        Create Account
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-border text-center">
                {{-- <p class="text-[10px] text-muted/60 font-mono bg-gray-100 dark:bg-white/5 inline-block px-2 py-1 rounded">
                    ENV: APP_ALLOW_TEMP_REGISTER=true
                </p> --}}
            </div>
        </div>
    </div>
</div>
@endsection