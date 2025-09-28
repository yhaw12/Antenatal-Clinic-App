<footer class="bg-gradient-to-r from-gray-900 to-gray-800 text-white py-8">
  <div class="max-w-7xl mx-auto px-4">
    {{-- <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <!-- Logo & Description -->
      <div class="md:col-span-2">
        <div class="flex items-center gap-3 mb-4">
          <img src="{{ asset('images/anc-logo.svg') }}" alt="ANC Clinic" class="h-10 w-auto">
          <div>
            <h3 class="text-xl font-bold">{{ config('app.name', 'ANC Clinic') }}</h3>
            <p class="text-gray-400 text-sm">Antenatal Care Management System</p>
          </div>
        </div>
        <p class="text-gray-400 text-sm leading-relaxed">Empowering healthcare providers with intuitive tools for better maternal care and patient management.</p>
        <div class="flex gap-4 mt-4">
          <a href="#" class="p-2 bg-white/10 rounded-full hover:bg-white/20 transition-all">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
          </a>
          <a href="#" class="p-2 bg-white/10 rounded-full hover:bg-white/20 transition-all">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          </a>
        </div>
      </div>

      <!-- Quick Links -->
      <div>
        <h4 class="text-sm font-semibold text-white mb-4 uppercase tracking-wide">Quick Links</h4>
        <ul class="space-y-2 text-sm">
          <li><a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white transition-colors">Dashboard</a></li>
          <li><a href="{{ route('patients.index') }}" class="text-gray-300 hover:text-white transition-colors">Patients</a></li>
          <li><a href="{{ route('appointments.index') }}" class="text-gray-300 hover:text-white transition-colors">Appointments</a></li>
          <li><a href="{{ route('reports.index') }}" class="text-gray-300 hover:text-white transition-colors">Reports</a></li>
        </ul>
      </div>

      <!-- Support -->
      <div>
        <h4 class="text-sm font-semibold text-white mb-4 uppercase tracking-wide">Support</h4>
        <ul class="space-y-2 text-sm">
          <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Help Center</a></li>
          <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Contact Us</a></li>
          <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Feedback</a></li>
          <li><a href="{{ route('settings.index') }}" class="text-gray-300 hover:text-white transition-colors">Settings</a></li>
        </ul>
      </div>

      <!-- Legal -->
      <div>
        <h4 class="text-sm font-semibold text-white mb-4 uppercase tracking-wide">Legal</h4>
        <ul class="space-y-2 text-sm">
          <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Privacy Policy</a></li>
          <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Terms of Service</a></li>
          <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Cookie Policy</a></li>
        </ul>
      </div>
    </div> --}}

    <!-- Bottom Bar -->
    <div class="border-t border-gray-700 mt-8 pt-6">
      <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <p class="text-sm text-gray-400">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <div class="flex gap-6 text-sm text-gray-400">
          <a href="#" class="hover:text-white transition-colors">Made with ❤️ for maternal health</a>
          <span>•</span>
          <a href="#" class="hover:text-white transition-colors">v{{ config('app.version', '1.0.0') }}</a>
        </div>
      </div>
    </div>
  </div>
</footer>