<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'STKALL - Sistem Pembelajaran')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eff6ff', 100:'#dbeafe', 500:'#3b82f6', 600:'#2563eb', 700:'#1d4ed8' }
                    }
                }
            }
        }
    </script>
    <style>
        .prose h1, .prose h2, .prose h3 { font-weight: 700; margin: 1rem 0 0.5rem; }
        .prose h1 { font-size: 1.5rem; }
        .prose h2 { font-size: 1.25rem; }
        .prose h3 { font-size: 1.1rem; }
        .prose p  { margin-bottom: 0.75rem; line-height: 1.7; }
        .prose ul, .prose ol { padding-left: 1.5rem; margin-bottom: 0.75rem; }
        .prose ul li { list-style-type: disc; }
        .prose ol li { list-style-type: decimal; }
        .prose strong { font-weight: 700; }
        .prose em { font-style: italic; }
        .prose code { background:#f1f5f9; padding:2px 6px; border-radius:4px; font-size:.875rem; }
        .prose pre  { background:#1e293b; color:#e2e8f0; padding:1rem; border-radius:8px; overflow-x:auto; margin-bottom:1rem; }
    </style>
    @yield('head')
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="font-bold text-gray-800 text-sm">STKALL</span>
            </div>
            @if(isset($student))
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ $student->name }}</span>
                <span class="text-gray-400">·</span>
                <span>{{ $student->class }}</span>
            </div>
            @endif
        </div>
    </nav>

    {{-- Flash messages --}}
    <div class="max-w-4xl mx-auto px-4 mt-4">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-start gap-3 mb-4">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-start gap-3 mb-4">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9v4a1 1 0 102 0V9a1 1 0 10-2 0zm0-4a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        @endif
        @if(session('info'))
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg flex items-start gap-3 mb-4">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm">{{ session('info') }}</p>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <main class="max-w-4xl mx-auto px-4 pb-16">
        @yield('content')
    </main>

    <footer class="text-center text-xs text-gray-400 py-6 border-t border-gray-200 mt-8">
        STKALL &copy; {{ date('Y') }} — Sistem Pembelajaran Interaktif
    </footer>

    @yield('scripts')
</body>
</html>
