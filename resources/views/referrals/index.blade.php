@extends('layouts.app')
@section('title','CHNS Referrals')
@section('page-title','CHNS Referrals')

@section('content')
  <div class="flex justify-between mb-4">
    <h2 class="text-xl font-semibold">CHNS Referrals</h2>
    <a href="/referrals/new" class="px-3 py-2 bg-blue-600 text-white rounded">Refer</a>
  </div>

  <div class="bg-white rounded shadow p-4">
    <div id="referrals-list"></div>
  </div>
@endsection

@push('scripts')
<script>
async function loadReferrals(){
  const res = await axios.get('/api/referrals');
  const cont = document.getElementById('referrals-list');
  cont.innerHTML = '';
  res.data.forEach(r => {
    const el = document.createElement('div');
    el.className = 'p-3 border-b';
    el.innerHTML = `<div><strong>${r.patient_name}</strong> — ${r.reason}</div><div class="text-sm text-gray-600">Referred to: ${r.referred_to_name || r.referred_to_user_id}</div>`;
    cont.appendChild(el);
  });
}
loadReferrals();
</script>
@endpush
