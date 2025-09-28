{{-- @extends('layouts.app')
@section('title','Registration disabled')

@section('content')
  <div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h1 class="text-xl font-semibold mb-4">Registration is disabled</h1>
    <p class="text-gray-700 mb-4">
      For security, user accounts are created by hospital administrators only.
      If you need an account, please contact your facility administrator.
    </p>
    <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Back to Login</a>
  </div>
@endsection --}}


@extends('layouts.app')

@section('title','Temporary Registration')

@section('content')
<div class="max-w-lg mx-auto mt-8 bg-white p-6 rounded shadow">
  <h1 class="text-xl font-semibold mb-4">Temporary Registration (Demo)</h1>

  <p class="text-sm text-gray-600 mb-4">
    This temporary registration is enabled for demonstration only. You must provide the registration token.
  </p>

  <form method="POST" action="{{ route('temp.register.post') }}">
    @csrf

    <div class="mb-3">
      <label class="block text-sm text-gray-600">Full name</label>
      <input name="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2" />
      @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
      <label class="block text-sm text-gray-600">Email</label>
      <input name="email" value="{{ old('email') }}" required class="w-full border rounded px-3 py-2" />
      @error('email') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="grid grid-cols-2 gap-3 mb-3">
      <div>
        <label class="block text-sm text-gray-600">Password</label>
        <input name="password" type="password" required class="w-full border rounded px-3 py-2" />
        @error('password') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>
      <div>
        <label class="block text-sm text-gray-600">Confirm Password</label>
        <input name="password_confirmation" type="password" required class="w-full border rounded px-3 py-2" />
      </div>
    </div>

    <div class="mb-3">
      <label class="block text-sm text-gray-600">Registration Token</label>
      <input name="token" value="{{ old('token') }}" required class="w-full border rounded px-3 py-2" />
      @error('token') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="flex gap-2">
      <button class="px-4 py-2 bg-teal-600 text-white rounded">Create account</button>
      <a href="{{ route('login') }}" class="text-gray-600 px-4 py-2">Cancel</a>
    </div>
  </form>

  <div class="mt-4 text-xs text-gray-500">
    After testing, disable temporary registration by setting <code>APP_ALLOW_TEMP_REGISTER=false</code> in your <code>.env</code>.
  </div>
</div>
@endsection

