@extends('layouts.app')
@section('title','Activity Log Detail')
@section('content')
<div class="bg-white p-4 rounded shadow">
  <h2 class="text-lg font-semibold mb-3">Activity Detail</h2>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <div class="text-xs text-gray-600">When</div>
      <div>{{ $log->created_at }}</div>
    </div>

    <div>
      <div class="text-xs text-gray-600">User</div>
      <div>{{ $log->user ? $log->user->name : 'system' }}</div>
    </div>

    <div>
      <div class="text-xs text-gray-600">Action</div>
      <div>{{ $log->action }}</div>
    </div>

    <div>
      <div class="text-xs text-gray-600">IP Address</div>
      <div>{{ $log->ip_address }}</div>
    </div>

    <div class="col-span-2">
      <div class="text-xs text-gray-600">Details</div>
      <div class="whitespace-pre-wrap mt-1">{{ $log->details ?? json_encode($log->meta, JSON_PRETTY_PRINT) }}</div>
    </div>
  </div>

  <div class="mt-4">
    <a href="{{ route('admin.activity-logs.index') }}" class="px-3 py-1 bg-gray-200 rounded">Back</a>
  </div>
</div>
@endsection
