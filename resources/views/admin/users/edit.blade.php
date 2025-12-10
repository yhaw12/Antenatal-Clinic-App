@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl px-4 py-8">
    
    <div class="card p-6 md:p-8">
        
        <div class="mb-8 pb-4" style="border-bottom: 1px solid var(--border);">
            <h2 class="text-2xl font-bold" style="color: var(--text);">Edit Profile</h2>
            <p class="text-sm mt-1" style="color: var(--muted);">Manage your public profile and security settings.</p>
        </div>

        <div aria-live="polite" class="mb-6 space-y-3">
            @if (session('success'))
                <div class="status-completed w-full p-4 rounded-[--radius-custom] flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="status-cancelled w-full p-4 rounded-[--radius-custom]">
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
                <label class="block text-sm font-semibold" style="color: var(--text);">Profile Picture</label>
                
                <div id="avatar-dropzone" 
                     class="group relative flex flex-col sm:flex-row items-center gap-6 p-6 rounded-[--radius-custom] transition-all cursor-pointer border-2 border-dashed"
                     style="background-color: var(--bg); border-color: var(--border);">
                    
                    <input id="avatar" name="avatar" type="file" accept="image/png, image/jpeg, image/jpg, image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    <div class="relative flex-shrink-0">
                        <img id="avatar-preview" 
                             src="{{ auth()->user()->avatar ? asset('storage/avatars/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=random' }}" 
                             alt="Profile Preview" 
                             class="w-24 h-24 rounded-full object-cover shadow-custom"
                             style="border: 4px solid var(--surface);">
                        
                        <div class="absolute inset-0 flex items-center justify-center rounded-full opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" style="background: rgba(0,0,0,0.4);">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>

                    <div class="flex-1 text-center sm:text-left">
                        <h3 class="text-sm font-medium" style="color: var(--text);">Click or drag new photo</h3>
                        <p class="text-xs mt-1" style="color: var(--muted);">SVG, PNG, JPG or WEBP (max. 5MB)</p>
                        <p id="file-name" class="text-sm font-medium mt-2 hidden" style="color: var(--brand);"></p>
                    </div>
                </div>
                @error('avatar') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium mb-1" style="color: var(--text);">Display Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" required
                       class="w-full px-4 py-2.5 rounded-lg border focus:ring-2 focus:ring-opacity-50 outline-none transition-shadow"
                       style="border-color: var(--border);">
                @error('name') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium mb-1" style="color: var(--text);">Email Address</label>
                <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required
                       class="w-full px-4 py-2.5 rounded-lg border focus:ring-2 focus:ring-opacity-50 outline-none transition-shadow"
                       style="border-color: var(--border);">
                @error('email') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
            </div>

            <hr class="my-6" style="border-color: var(--border);">

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="password" class="block text-sm font-medium mb-1" style="color: var(--text);">New Password (Optional)</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="new-password" placeholder="Min. 8 characters"
                               class="w-full px-4 py-2.5 rounded-lg border focus:ring-2 focus:ring-opacity-50 outline-none transition-shadow pr-10"
                               style="border-color: var(--border);">
                        <button type="button" class="toggle-pw absolute right-3 top-2.5 outline-none" style="color: var(--muted);" data-target="password">
                            <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <div class="mt-2 h-1 w-full rounded-full overflow-hidden" style="background-color: var(--border);">
                        <div id="pw-strength-bar" class="h-full w-0 transition-all duration-300 ease-out bg-danger"></div>
                    </div>
                    @error('password') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-1" style="color: var(--text);">Confirm Password</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                               class="w-full px-4 py-2.5 rounded-lg border focus:ring-2 focus:ring-opacity-50 outline-none transition-shadow pr-10"
                               style="border-color: var(--border);">
                        <button type="button" class="toggle-pw absolute right-3 top-2.5 outline-none" style="color: var(--muted);" data-target="password_confirmation">
                            <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-8 pt-6" style="border-top: 1px solid var(--border);">
                <button type="button" onclick="window.history.back()" class="btn-ghost">Cancel</button>
                <button id="submit-btn" type="submit" class="btn-primary">
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
    // --- 1. Dropzone Logic ---
    const dropzone = document.getElementById('avatar-dropzone');
    const input = document.getElementById('avatar');
    const preview = document.getElementById('avatar-preview');
    const fileNameDisplay = document.getElementById('file-name');

    input.addEventListener('change', (e) => {
        const file = e.target.files[0];
        updatePreview(file);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.style.borderColor = 'var(--brand)';
            dropzone.style.backgroundColor = 'color-mix(in srgb, var(--brand) 10%, transparent)';
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropzone.style.borderColor = 'var(--border)';
            dropzone.style.backgroundColor = 'var(--bg)';
        });
    });

    dropzone.addEventListener('drop', (e) => {
        const file = e.dataTransfer.files[0];
        if (file) {
            input.files = e.dataTransfer.files;
            updatePreview(file);
        }
    });

    function updatePreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        preview.src = URL.createObjectURL(file);
        fileNameDisplay.textContent = `Selected: ${file.name}`;
        fileNameDisplay.classList.remove('hidden');
        preview.onload = () => URL.revokeObjectURL(preview.src);
    }

    // --- 2. Password Toggle & Strength ---
    const pwInput = document.getElementById('password');
    const pwBar = document.getElementById('pw-strength-bar');

    if (pwInput) {
        pwInput.addEventListener('input', function() {
            const val = this.value;
            let strength = 0;
            if(val.length > 5) strength += 20;
            if(val.length > 8) strength += 20;
            if(/[A-Z]/.test(val)) strength += 20;
            if(/[0-9]/.test(val)) strength += 20;
            if(/[^A-Za-z0-9]/.test(val)) strength += 20;

            pwBar.style.width = `${strength}%`;
            
            // Note: Using JS to set color classes, but relying on CSS variables
            pwBar.className = 'h-full transition-all duration-300 ease-out';
            if(strength < 40) pwBar.style.backgroundColor = 'var(--danger)';
            else if(strength < 80) pwBar.style.backgroundColor = 'var(--accent)'; // Yellowish often maps to accent in some themes, or fix to specific color
            else pwBar.style.backgroundColor = 'var(--success)';
        });
    }

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

    // --- 3. Submit Loading ---
    const form = document.getElementById('profile-form');
    if (form) {
        form.addEventListener('submit', () => {
            const btn = document.getElementById('submit-btn');
            const spinner = document.getElementById('btn-spinner');
            const text = document.getElementById('btn-text');
            
            btn.disabled = true;
            btn.style.opacity = '0.7';
            spinner.classList.remove('hidden');
            text.textContent = 'Saving...';
        });
    }
});
</script>
@endpush