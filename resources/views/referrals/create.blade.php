@extends('layouts.app')
@section('title','Refer to CHNS')
@section('page-title','New CHNS Referral')

@section('content')
  <div class="bg-white rounded shadow p-6 max-w-3xl">
    <form method="POST" action="{{ route('referrals.store') }}">
      @csrf
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="text-sm text-gray-600">Patient</label>
          <select name="patient_id" class="w-full border rounded px-3 py-2" required>
            <option value="">--select patient--</option>
            @foreach($patients as $p)
              <option value="{{ $p->id }}">{{ $p->first_name }} {{ $p->last_name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="text-sm text-gray-600">CHNS (Assign)</label>
          <select name="referred_to_user_id" class="w-full border rounded px-3 py-2" required>
            <option value="">--select CHNS--</option>
            @foreach($chns as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-span-2">
          <label class="text-sm text-gray-600">Reason / Clinical notes</label>
          <textarea name="reason" required class="w-full border rounded px-3 py-2" rows="4"></textarea>
        </div>
      </div>

      <div class="mt-4">
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Refer</button>
        <a href="{{ route('referrals.index') }}" class="ml-2 text-gray-600">Cancel</a>
      </div>
    </form>
  </div>
@endsection
