@extends('layouts.app')
@section('title','New Call')
@section('page-title','New Call')

@section('content')
  <div class="bg-white rounded shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('call-logs.store') }}">
      @csrf
      <input type="hidden" name="appointment_id" value="{{ request('appointment_id') }}">
      <div class="grid grid-cols-1 gap-4">
        <div>
          <label class="text-sm text-gray-600">Patient</label>
          <div class="p-2 border rounded bg-gray-50">
            {{ $patient->first_name ?? ($appointment->patient->first_name ?? 'Unknown') ?? '' }}
            {{ $patient->last_name ?? ($appointment->patient->last_name ?? '') ?? '' }}
          </div>
          <input type="hidden" name="patient_id" value="{{ $patient->id ?? ($appointment->patient->id ?? '') }}">
        </div>

        <div>
          <label class="text-sm text-gray-600">Result</label>
          <select name="result" required class="w-full border rounded px-3 py-2">
            <option value="no_answer">No answer</option>
            <option value="rescheduled">Rescheduled</option>
            <option value="will_attend">Will attend</option>
            <option value="refused">Refused</option>
            <option value="incorrect_number">Incorrect number</option>
          </select>
        </div>

        <div>
          <label class="text-sm text-gray-600">Notes</label>
          <textarea name="notes" class="w-full border rounded px-3 py-2" rows="3"></textarea>
        </div>

        <div class="flex gap-2">
          <button class="px-4 py-2 bg-blue-600 text-white rounded">Save Call</button>
          <a href="{{ route('call-logs') }}" class="px-4 py-2 text-gray-600">Cancel</a>
        </div>
      </div>
    </form>
  </div>
@endsection
