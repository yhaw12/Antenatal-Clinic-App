@extends('layouts.app')
@section('title','Create User')
@section('page-title','Create New User (Admin)')

@section('content')
  <div class="bg-white rounded shadow p-6 max-w-2xl">
    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
      <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">{{ session('warning') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}">
      @csrf
      <div class="grid grid-cols-1 gap-4">
        <div>
          <label class="block text-sm text-gray-600">Full name</label>
          <input name="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2" />
          @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="block text-sm text-gray-600">Email</label>
          <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded px-3 py-2" />
          @error('email') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="block text-sm text-gray-600">Role</label>
          <select name="role" class="w-full border rounded px-3 py-2" required>
            <option value="">-- select role --</option>
            @foreach($roles as $r)
              <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
            @endforeach
          </select>
          @error('role') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <p class="text-sm text-gray-600">A password reset email will be sent so the user can set their own password.</p>
        </div>

        <div class="flex gap-2">
          <button class="px-4 py-2 bg-blue-600 text-white rounded">Create User</button>
          <a href="{{ route('dashboard') }}" class="px-4 py-2 text-gray-600">Cancel</a>
        </div>
      </div>
    </form>
  </div>
@endsection
