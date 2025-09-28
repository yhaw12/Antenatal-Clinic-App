{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl px-4 py-8">
    <div class="bg-white dark:bg-[#0b1220] rounded-2xl shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Edit Profile</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Update your personal information and profile picture.</p>

        <div aria-live="polite" class="mb-4">
            @if (session('success'))
                <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-3 text-green-800 dark:text-green-200 flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <div class="flex-1">{{ session('success') }}</div>
                    <button type="button" class="text-sm text-gray-500 dark:text-gray-300" onclick="this.closest('[aria-live]').remove()">Close</button>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-md bg-red-50 dark:bg-red-900/20 p-3 text-red-800 dark:text-red-200 flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    <div class="flex-1">{{ session('error') }}</div>
                    <button type="button" class="text-sm text-gray-500 dark:text-gray-300" onclick="this.closest('[aria-live]').remove()">Close</button>
                </div>
            @endif
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-4 rounded-md bg-red-50 dark:bg-red-900/20 p-3 text-red-800 dark:text-red-200">
                <strong class="block font-medium">Fix the following:</strong>
                <ul class="mt-2 list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Profile picture</label>
                <div id="avatar-dropzone" class="relative border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg p-4 flex flex-col md:flex-row items-center gap-4 bg-gray-50 dark:bg-gray-900">
                    <div class="flex-shrink-0">
                        <img id="avatar-preview" src="{{ auth()->user()->avatar ? asset('storage/avatars/' . auth()->user()->avatar) : asset('images/default-avatar.png') }}" alt="Avatar" class="w-28 h-28 rounded-full object-cover border border-gray-200 dark:border-gray-700">
                    </div>

                    <div class="flex-1 w-full">
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Drag & drop an image here or choose a file. Max 4MB server-side. Allowed: JPG, PNG, GIF, WEBP.</p>

                        <div class="flex items-center gap-3">
                            <label for="avatar" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border rounded-md cursor-pointer text-sm shadow-sm hover:bg-gray-100 dark:hover:bg-gray-800">
                                Choose file
                                <input id="avatar" name="avatar" type="file" accept="image/*" class="sr-only">
                            </label>

                            <div id="avatar-info" class="text-sm text-gray-500 dark:text-gray-400">No file selected</div>
                        </div>

                        <p id="avatar-error" role="alert" class="mt-2 text-sm text-red-600 dark:text-red-400 hidden"></p>
                        <p id="avatar-dim" class="mt-2 text-xs text-gray-500 dark:text-gray-400 hidden"></p>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" required
                       class="mt-1 block w-full p-3 rounded-lg border dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500"
                       aria-describedby="name-help">
                <p id="name-help" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Your display name as shown in the app.</p>
                @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required
                       class="mt-1 block w-full p-3 rounded-lg border dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500"
                       aria-describedby="email-help">
                <p id="email-help" class="text-xs text-gray-500 dark:text-gray-400 mt-1">We'll send account notifications here.</p>
                @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New password (optional)</label>
                <div class="relative mt-1">
                    <input id="password" name="password" type="password" autocomplete="new-password"
                           class="block w-full p-3 pr-12 rounded-lg border dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <button id="toggle-password" type="button" aria-label="Show password" class="absolute right-2 top-2/4 -translate-y-2/4 text-sm text-gray-500 dark:text-gray-300">Show</button>
                </div>
                <div class="mt-2">
                    <div id="pw-strength" class="h-2 w-full bg-gray-200 dark:bg-gray-800 rounded overflow-hidden">
                        <div id="pw-strength-bar" class="h-full w-0 bg-red-500 transition-all"></div>
                    </div>
                    <p id="pw-strength-text" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Password strength: <span id="pw-text">—</span></p>
                </div>
                @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                       class="mt-1 block w-full p-3 rounded-lg border dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                <p id="pw-match" class="text-sm mt-1"></p>
            </div>

            <input type="hidden" name="avatar_uploaded" id="avatar_uploaded" value="{{ old('avatar_uploaded', auth()->user()->avatar ?? '') }}">

            <div class="flex justify-end">
                <button id="submit-btn" type="submit" class="inline-flex items-center gap-3 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60">
                    <svg id="btn-spinner" class="hidden animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                        <path d="M4 12a8 8 0 018-8v8z" fill="currentColor" class="opacity-75"></path>
                    </svg>
                    <span id="btn-text">Update profile</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Elements
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatar-preview');
    const avatarInfo = document.getElementById('avatar-info');
    const avatarError = document.getElementById('avatar-error');
    const avatarDropzone = document.getElementById('avatar-dropzone');
    const form = document.getElementById('profile-form');
    const submitBtn = document.getElementById('submit-btn');

    const MAX_WIDTH = 800;
    const OUTPUT_QUALITY = 0.8;
    const MAX_CLIENT_SIZE = 10 * 1024 * 1024; // 10MB client-side hard cutoff

    function fileToImage(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onerror = () => reject(new Error('Failed to read file'));
            reader.onload = () => {
                const img = new Image();
                img.onerror = () => reject(new Error('Invalid image'));
                img.onload = () => resolve(img);
                img.src = reader.result;
            };
            reader.readAsDataURL(file);
        });
    }

    async function resizeImageFile(file, maxWidth = MAX_WIDTH, quality = OUTPUT_QUALITY) {
        if (!file.type.startsWith('image/')) return file;

        const img = await fileToImage(file);
        const width = img.width;
        const height = img.height;

        let targetWidth = width;
        let targetHeight = height;
        if (width > maxWidth) {
            targetWidth = maxWidth;
            targetHeight = Math.round((height * maxWidth) / width);
        }

        const canvas = document.createElement('canvas');
        canvas.width = targetWidth;
        canvas.height = targetHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, targetWidth, targetHeight);

        let outType = 'image/jpeg';
        if (file.type === 'image/png' || file.type === 'image/webp') outType = file.type;

        return await new Promise((resolve) => {
            if (file.type === 'image/gif') {
                // preserve animated gifs - do not re-encode
                resolve(file);
                return;
            }
            canvas.toBlob((blob) => {
                if (!blob) {
                    resolve(file);
                    return;
                }
                resolve(blob);
            }, outType, quality);
        });
    }

    function blobToFile(theBlob, fileName, mimeType) {
        try {
            return new File([theBlob], fileName, { type: mimeType, lastModified: Date.now() });
        } catch (e) {
            theBlob.name = fileName;
            theBlob.lastModified = Date.now();
            return theBlob;
        }
    }

    function uploadAvatarXHR(file, onProgress) {
        return new Promise((resolve, reject) => {
            const url = "{{ route('profile.avatar') }}";
            const formData = new FormData();
            formData.append('avatar', file);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', url, true);
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) xhr.setRequestHeader('X-CSRF-TOKEN', token);

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable && typeof onProgress === 'function') {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    onProgress(percent);
                }
            };

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    const status = xhr.status;
                    let json = null;
                    try { json = JSON.parse(xhr.responseText || '{}'); } catch (err) {}
                    if (status >= 200 && status < 300) {
                        resolve(json);
                    } else {
                        reject(json || { message: 'Upload failed', status });
                    }
                }
            };

            xhr.onerror = function() {
                reject({ message: 'Network error during upload' });
            };

            xhr.send(formData);
        });
    }

    async function handleFileAndUpload(rawFile) {
        avatarError.classList.add('hidden');
        avatarInfo.textContent = 'Processing...';

        try {
            if (rawFile.size > MAX_CLIENT_SIZE) {
                throw new Error('File too large (over 10MB).');
            }

            const blob = await resizeImageFile(rawFile, MAX_WIDTH, OUTPUT_QUALITY);
            const ext = rawFile.name.split('.').pop();
            const outMime = blob.type || 'image/jpeg';
            const filename = `avatar_${Date.now()}.${ext || (outMime.includes('png') ? 'png' : 'jpg')}`;
            const outFile = blobToFile(blob, filename, outMime);

            const previewUrl = URL.createObjectURL(outFile);
            avatarPreview.src = previewUrl;
            avatarInfo.textContent = `${outFile.name} • ${Math.round(outFile.size / 1024)} KB (uploading...)`;

            submitBtn.setAttribute('disabled', 'disabled');

            const progressEl = document.createElement('span');
            progressEl.className = 'ml-2 text-xs text-gray-500';
            avatarInfo.appendChild(progressEl);

            const resp = await uploadAvatarXHR(outFile, (p) => {
                progressEl.textContent = ` ${p}%`;
            });

            if (resp && resp.success) {
                if (resp.avatar_url) avatarPreview.src = resp.avatar_url;
                avatarInfo.textContent = 'Uploaded ✓';

                let hidden = document.getElementById('avatar_uploaded');
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'avatar_uploaded';
                    hidden.id = 'avatar_uploaded';
                    form.appendChild(hidden);
                }
                hidden.value = resp.filename || '';

                submitBtn.removeAttribute('disabled');
            } else {
                throw new Error((resp && resp.message) ? resp.message : 'Upload error');
            }
        } catch (err) {
            console.error(err);
            avatarError.textContent = err.message || 'Failed to process/upload image';
            avatarError.classList.remove('hidden');
            avatarInfo.textContent = 'Upload failed';
            submitBtn.removeAttribute('disabled');
        }
    }

    // input change
    avatarInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) handleFileAndUpload(file);
    });

    // drag & drop
    ['dragenter','dragover'].forEach(name => {
        avatarDropzone.addEventListener(name, (e) => {
            e.preventDefault();
            avatarDropzone.classList.add('ring-2','ring-blue-400');
        });
    });
    ['dragleave','drop','dragend'].forEach(name => {
        avatarDropzone.addEventListener(name, (e) => {
            e.preventDefault();
            avatarDropzone.classList.remove('ring-2','ring-blue-400');
        });
    });
    avatarDropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        const dt = e.dataTransfer;
        if (!dt || !dt.files || !dt.files.length) return;
        const file = dt.files[0];
        const dt2 = new DataTransfer();
        dt2.items.add(file);
        avatarInput.files = dt2.files;
        handleFileAndUpload(file);
    });

    // password UX
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const togglePwBtn = document.getElementById('toggle-password');
    const pwStrengthBar = document.getElementById('pw-strength-bar');
    const pwText = document.getElementById('pw-text');
    const pwMatch = document.getElementById('pw-match');

    function passwordScore(pw) {
        let score = 0;
        if (!pw) return score;
        if (pw.length >= 8) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;
        return score;
    }

    function updatePwStrength() {
        const pw = passwordInput.value;
        const score = passwordScore(pw);
        const pct = (score / 4) * 100;
        pwStrengthBar.style.width = pct + '%';
        let color = 'bg-red-500';
        let text = 'Too weak';
        if (score === 1) { color = 'bg-yellow-400'; text = 'Weak'; }
        if (score === 2) { color = 'bg-yellow-400'; text = 'Fair'; }
        if (score === 3) { color = 'bg-green-400'; text = 'Good'; }
        if (score === 4) { color = 'bg-green-600'; text = 'Strong'; }
        pwStrengthBar.className = 'h-full transition-all ' + color;
        pwText.textContent = text;
    }

    function checkPwMatch() {
        const pw = passwordInput.value;
        const cpw = confirmInput.value;
        if (!pw && !cpw) {
            pwMatch.textContent = '';
            return;
        }
        if (pw === cpw) {
            pwMatch.textContent = 'Passwords match';
            pwMatch.className = 'text-sm text-green-600 dark:text-green-300 mt-1';
        } else {
            pwMatch.textContent = 'Passwords do not match';
            pwMatch.className = 'text-sm text-red-600 dark:text-red-400 mt-1';
        }
    }

    if (passwordInput) passwordInput.addEventListener('input', () => { updatePwStrength(); checkPwMatch(); });
    if (confirmInput) confirmInput.addEventListener('input', checkPwMatch);
    if (togglePwBtn) togglePwBtn.addEventListener('click', () => {
        if (!passwordInput) return;
        const show = passwordInput.type === 'password';
        passwordInput.type = show ? 'text' : 'password';
        confirmInput.type = show ? 'text' : 'password';
        togglePwBtn.textContent = show ? 'Hide' : 'Show';
    });

    // prevent form submit if passwords do not match
    form.addEventListener('submit', (e) => {
        if (passwordInput && passwordInput.value && passwordInput.value !== confirmInput.value) {
            e.preventDefault();
            pwMatch.textContent = 'Passwords do not match';
            pwMatch.className = 'text-sm text-red-600 dark:text-red-400 mt-1';
            passwordInput.focus();
            return;
        }

        // show spinner & disable
        document.getElementById('btn-spinner').classList.remove('hidden');
        document.getElementById('btn-text').textContent = 'Updating...';
        submitBtn.setAttribute('disabled', 'disabled');
    });
});
</script>
@endpush
