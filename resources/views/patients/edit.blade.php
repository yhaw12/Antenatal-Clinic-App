@extends('layouts.app')
@section('title','Edit Patient')
@section('page-title','Edit Patient')

@section('content')
  <div class="card max-w-2xl">
    <form method="POST" action="{{ route('patients.update', $patient->id) }}" class="space-y-4">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="first_name" class="label">First name</label>
          <input id="first_name" name="first_name" value="{{ old('first_name', $patient->first_name) }}" required class="input" />
          @error('first_name')
            <p class="text-danger text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="last_name" class="label">Last name</label>
          <input id="last_name" name="last_name" value="{{ old('last_name', $patient->last_name) }}" class="input" />
          @error('last_name')
            <p class="text-danger text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="phone" class="label">Phone</label>
          <input id="phone" name="phone" value="{{ old('phone', $patient->phone) }}" class="input" />
          @error('phone')
            <p class="text-danger text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="whatsapp" class="label">WhatsApp</label>
          <input id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $patient->whatsapp) }}" class="input" />
          @error('whatsapp')
            <p class="text-danger text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div class="md:col-span-2">
          <label for="address" class="label">Address</label>
          <input id="address" name="address" value="{{ old('address', $patient->address) }}" class="input" />
          @error('address')
            <p class="text-danger text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="next_of_kin_name" class="label">Next of kin</label>
          <input id="next_of_kin_name" name="next_of_kin_name" value="{{ old('next_of_kin_name', $patient->next_of_kin_name) }}" class="input" />
          @error('next_of_kin_name')
            <p class="text-danger text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="next_of_kin_phone" class="label">Next of kin phone</label>
          <input id="next_of_kin_phone" name="next_of_kin_phone" value="{{ old('next_of_kin_phone', $patient->next_of_kin_phone) }}" class="input" />
          @error('next_of_kin_phone')
            <p class="text-danger text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="flex gap-2 pt-4">
        <button type="submit" class="btn-primary">Save changes</button>
        <a href="{{ route('patients.show', $patient->id) }}" class="btn-ghost">Cancel</a>
      </div>
    </form>
  </div>
@endsection
