@extends('layouts.app')

@section('title','Login — ANC Clinic')

@section('content')
<div class="bg-white shadow rounded overflow-hidden">
  <div class="h-2 bg-teal-600"></div>

  <div class="md:flex">
    {{-- LEFT: welcome/banner --}}
    <section class="md:w-2/3 p-6">
      <h2 class="text-xl font-semibold text-gray-700 mb-2">Welcome to the ANC Clinic</h2>
      <p class="text-sm text-gray-600 mb-4">Please enter a valid username and password to access the Antenatal Care dashboard.</p>

      <div class="grid grid-cols-2 gap-4">
        <div class="rounded overflow-hidden border">
          <img src="{{ asset('images/anc-banner-1.svg') }}" alt="Maternal care" class="w-full h-40 object-cover">
        </div>

        <div class="rounded overflow-hidden border">
          <img src="{{ asset('images/anc-banner-2.svg') }}" alt="Clinic care" class="w-full h-40 object-cover">
        </div>
      </div>

      <div class="mt-6 text-xs text-gray-500">
        <div>Contact: anc@{{ request()->getHost() }} | Phone: +233 20 000 0000</div>
      </div>
    </section>

    {{-- RIGHT: login panel --}}
    <aside class="md:w-1/3 bg-gray-50 p-6 border-l">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">Login Panel</h3>

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
          <label class="block text-sm text-gray-600 mb-1">Username / Email</label>
          <input name="email" value="{{ old('email') }}" required autofocus
                 class="w-full border rounded px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-300" />
          @error('email') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="block text-sm text-gray-600 mb-1">Password</label>
          <input name="password" type="password" required
                 class="w-full border rounded px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-300" />
          @error('password') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="flex items-center justify-between mb-4">
          <label class="flex items-center text-sm gap-2">
            <input type="checkbox" name="remember" class="form-checkbox" /> <span class="text-sm text-gray-600">Remember me</span>
          </label>
          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-sm text-teal-600">Forgot password?</a>
          @endif
        </div>

        <div class="flex items-center gap-2">
          <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded shadow hover:bg-teal-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7a4 4 0 10-8 0v4M5 11v8h14v-8"></path></svg>
            <span>Secure Login</span>
          </button>

          <a href="#" class="text-sm text-gray-600">Help</a>
        </div>

      </form>

      {{-- Temporary registration link (shows only when enabled in .env) --}}
      @if(env('APP_ALLOW_TEMP_REGISTER', false))
        <div class="mt-4 text-center">
          <a href="{{ route('temp.register') }}"
             class="inline-block mt-2 text-sm bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">
            Create an account (Temporary)
          </a>
          <div class="text-xs text-gray-500 mt-2">Temporary registration — disable before production.</div>
        </div>
      @endif

      <div class="mt-6 text-xs text-gray-500">
        <div>Please log out after use. Patient data is confidential and protected.</div>
      </div>
    </aside>
  </div>
</div>
@endsection
