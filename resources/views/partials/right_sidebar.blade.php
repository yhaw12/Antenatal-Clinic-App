{{-- right_sidebar --}}
<div class="text-sm font-semibold bg-blue-900 text-white px-3 py-2 rounded">Patient Search</div>

<div class="mt-3 space-y-2">
  <div><input class="search-input w-full" placeholder="By Patient Name"></div>
  <div><input class="search-input w-full" placeholder="By Patient No."></div>
  <div><input class="search-input w-full" placeholder="By Patient Clinic"></div>
  <div><input class="search-input w-full" placeholder="By Area"></div>
  <div><input class="search-input w-full" placeholder="By Mobile"></div>
  <div><input class="search-input w-full" placeholder="By MOH No."></div>
  <div><input class="search-input w-full" placeholder="By Patient Tag"></div>
  <div class="mt-2">
    <button class="w-full bg-blue-600 text-white py-2 rounded text-sm">Search</button>
  </div>
</div>

<div class="mt-4">
  <div class="flex gap-2">
    <button class="px-2 py-1 border rounded text-sm">Support</button>
    <button class="px-2 py-1 border rounded text-sm">Help</button>
  </div>

  <div class="mt-3">
    <div class="server-msg mt-3">
      <div class="font-semibold">Server Messages</div>
      <div class="text-xs mt-1">
        @php
          $serverMessages = $serverMessages ?? [
            "Don't forget to Log Out before leaving.",
            "Support is just a click away."
          ];
        @endphp
        @foreach($serverMessages as $m)
          <div class="mt-1">- {{ $m }}</div>
        @endforeach
      </div>
    </div> 
  </div>
</div>
