@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl px-4 py-8">
    <div class="bg-white dark:bg-[#0b1220] rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 p-6 transition-all duration-300">
        
        <div class="mb-8 border-b border-gray-100 dark:border-gray-800 pb-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Profile</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your public profile and security settings.</p>
        </div>

        <div aria-live="polite" class="mb-6 space-y-3">
            @if (session('success'))
                <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800 flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                    <p class="font-medium flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Please check the errors below
                    </p>
                </div>
            @endif
        </div>

        <form id="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Profile Picture</label>
                
                <div id="avatar-dropzone" class="group relative flex flex-col sm:flex-row items-center gap-6 p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:border-blue-400 dark:hover:border-blue-500 transition-all cursor-pointer">
                    
                    <input id="avatar" name="avatar" type="file" accept="image/png, image/jpeg, image/jpg, image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    <div class="relative flex-shrink-0">
                        <img id="avatar-preview" 
                             src="{{ auth()->user()->avatar ? asset('storage/avatars/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=random' }}" 
                             alt="Profile Preview" 
                             class="w-24 h-24 rounded-full object-cover ring-4 ring-white dark:ring-gray-800 shadow-md">
                        
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>

                    <div class="flex-1 text-center sm:text-left">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Click or drag new photo</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">SVG, PNG, JPG or WEBP (max. 5MB)</p>
                        <p id="file-name" class="text-sm text-blue-600 dark:text-blue-400 font-medium mt-2 hidden"></p>
                    </div>
                </div>
                @error('avatar') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <hr class="border-gray-200 dark:border-gray-700 my-6">

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password (Optional)</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="new-password" placeholder="Min. 8 characters"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10">
                        <button type="button" class="toggle-pw absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" data-target="password">
                            <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <div class="mt-2 h-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div id="pw-strength-bar" class="h-full w-0 transition-all duration-300 ease-out bg-red-500"></div>
                    </div>
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10">
                        <button type="button" class="toggle-pw absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" data-target="password_confirmation">
                            <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
                <button type="button" onclick="window.history.back()" class="px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">Cancel</button>
                <button id="submit-btn" type="submit" class="relative inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/20 transition-all disabled:opacity-70 disabled:cursor-not-allowed shadow-lg shadow-blue-500/30">
                    <svg id="btn-spinner" class="hidden animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span id="btn-text">Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- 1. Robust Image Preview & Drag/Drop ---
    const dropzone = document.getElementById('avatar-dropzone');
    const input = document.getElementById('avatar');
    const preview = document.getElementById('avatar-preview');
    const fileNameDisplay = document.getElementById('file-name');

    // Handle File Selection (via click or drag)
    input.addEventListener('change', (e) => {
        const file = e.target.files[0];
        updatePreview(file);
    });

    // Drag Effects
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
        });
    });

    dropzone.addEventListener('drop', (e) => {
        const file = e.dataTransfer.files[0];
        if (file) {
            input.files = e.dataTransfer.files; // Assign to input
            updatePreview(file);
        }
    });

    function updatePreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        
        // Use createObjectURL for instant preview (no FileReader lag)
        preview.src = URL.createObjectURL(file);
        
        // Show file name
        fileNameDisplay.textContent = `Selected: ${file.name}`;
        fileNameDisplay.classList.remove('hidden');

        // Free memory when image loads
        preview.onload = () => URL.revokeObjectURL(preview.src);
    }

    // --- 2. Password Strength & Toggle ---
    const pwInput = document.getElementById('password');
    const pwBar = document.getElementById('pw-strength-bar');

    pwInput.addEventListener('input', function() {
        const val = this.value;
        let strength = 0;
        if(val.length > 5) strength += 20;
        if(val.length > 8) strength += 20;
        if(/[A-Z]/.test(val)) strength += 20;
        if(/[0-9]/.test(val)) strength += 20;
        if(/[^A-Za-z0-9]/.test(val)) strength += 20;

        pwBar.style.width = `${strength}%`;
        
        // Color logic
        pwBar.className = 'h-full transition-all duration-300 ease-out';
        if(strength < 40) pwBar.classList.add('bg-red-500');
        else if(strength < 80) pwBar.classList.add('bg-yellow-500');
        else pwBar.classList.add('bg-green-500');
    });

    // Toggle Eye Icon
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const inputId = btn.getAttribute('data-target');
            const inputEl = document.getElementById(inputId);
            const isPassword = inputEl.type === 'password';
            
            inputEl.type = isPassword ? 'text' : 'password';
            btn.querySelector('.eye-open').classList.toggle('hidden');
            btn.querySelector('.eye-closed').classList.toggle('hidden');
        });
    });

    // --- 3. Form Submit Loading State ---
    const form = document.getElementById('profile-form');
    form.addEventListener('submit', () => {
        const btn = document.getElementById('submit-btn');
        const spinner = document.getElementById('btn-spinner');
        const text = document.getElementById('btn-text');
        
        btn.disabled = true;
        spinner.classList.remove('hidden');
        text.textContent = 'Saving...';
    });
});
</script>
@endpush