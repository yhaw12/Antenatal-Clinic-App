@extends('layouts.app')
@section('title','Activity Logs')
@section('content')
<div class="bg-white p-4 rounded shadow">
  <h2 class="text-lg font-semibold mb-3">Activity Logs</h2>

  <form method="GET" class="flex gap-2 items-end mb-3">
    <div>
      <label class="text-xs">User</label>
      <select name="user_id" class="border px-2 py-1 rounded">
        <option value="">All</option>
        @foreach(\App\Models\User::orderBy('name')->get() as $u)
          <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-xs">Action</label>
      <select name="action" class="border px-2 py-1 rounded">
        <option value="">All</option>
        @foreach($actions as $a)
          <option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ $a }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-xs">From</label>
      <input type="date" name="from" value="{{ request('from') }}" class="border px-2 py-1 rounded">
    </div>

    <div>
      <label class="text-xs">To</label>
      <input type="date" name="to" value="{{ request('to') }}" class="border px-2 py-1 rounded">
    </div>

    <div>
      <button class="px-3 py-1 bg-blue-600 text-white rounded">Filter</button>
    </div>
  </form>

  <div class="overflow-auto">
    <table class="w-full table-auto">
      <thead>
        <tr class="text-left text-sm text-gray-600">
          <th class="p-2">When</th>
          <th class="p-2">User</th>
          <th class="p-2">Action</th>
          <th class="p-2">Details</th>
          <th class="p-2">IP</th>
          <th class="p-2"> </th>
        </tr>
      </thead>
      <tbody>
        @foreach($logs as $log)
        <tr class="border-t">
          <td class="p-2 text-sm">{{ $log->created_at->format('Y-m-d H:i') }}</td>
          <td class="p-2 text-sm">{{ $log->user ? $log->user->name : 'system' }}</td>
          <td class="p-2 text-sm">{{ strtoupper($log->action) }}</td>
          <td class="p-2 text-sm truncate max-w-xl">{{ $log->details ?? json_encode($log->meta) }}</td>
          <td class="p-2 text-sm">{{ $log->ip_address }}</td>
          <td class="p-2 text-sm">
            <a href="{{ route('admin.activity-logs.show', $log->id) }}" class="text-blue-600">View</a>
            <form action="{{ route('admin.activity-logs.destroy', $log->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this log?');">
              @csrf @method('DELETE')
              <button class="text-red-600 ml-2">Delete</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $logs->links() }}
  </div>
</div>
@endsection
