@extends('layouts.app')
@section('title','Exports')
@section('page-title','Exports')

@section('content')
  <div class="bg-white rounded shadow p-4">
    <h3 class="font-semibold mb-4">Export Data</h3>
    <form id="export-form" method="POST" action="{{ route('exports.queue') }}">
      @csrf
      <div class="flex gap-2">
        <input type="date" name="date_from" class="border rounded px-3 py-2" required>
        <input type="date" name="date_to" class="border rounded px-3 py-2" required>
        <button class="px-3 py-2 bg-blue-600 text-white rounded">Queue Export</button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
<script>
document.getElementById('export-form').addEventListener('submit', async function(e){
  e.preventDefault();
  const f = new FormData(this);
  const res = await axios.post(this.action, Object.fromEntries(f));
  alert('Export queued, id: ' + res.data.export_id);
});
</script>
@endpush
