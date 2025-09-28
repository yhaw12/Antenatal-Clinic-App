@extends('layouts.app')
@section('title','Reports')
@section('page-title','Reports')

@section('content')
  <div class="bg-white rounded shadow p-4 max-w-3xl">
    <h3 class="font-semibold mb-4">Generate Report</h3>
    <form id="report-form" action="{{ route('reports.generate') }}" method="GET">
      <div class="flex gap-2">
        <input type="date" name="from" class="border rounded px-3 py-2" required>
        <input type="date" name="to" class="border rounded px-3 py-2" required>
        <button class="px-3 py-2 bg-blue-600 text-white rounded">Generate</button>
      </div>
    </form>
  </div>
@endsection
