<footer class="mt-auto py-8 transition-colors duration-300"
        style="background-color: var(--surface); border-top: 1px solid var(--border); color: var(--muted);">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            
            <p class="text-sm" style="color: var(--muted);">
                &copy; {{ date('Y') }} 
                <span class="font-medium" style="color: var(--text);">{{ config('app.name') }}</span>. 
                All rights reserved.
            </p>

            <div class="flex items-center gap-6 text-sm">
                <span class="flex items-center gap-1 transition-colors cursor-default hover:opacity-80"
                      style="color: var(--muted);">
                    Made with <span class="text-red-500 animate-pulse">❤️</span> for maternal health
                </span>

                <span style="color: var(--border);">•</span>

                <span class="font-mono text-xs px-2 py-1 rounded border transition-colors"
                      style="background-color: var(--bg); color: var(--text); border-color: var(--border);">
                    v{{ config('app.version', '1.0.0') }}
                </span>
            </div>

        </div>
    </div>
</footer>