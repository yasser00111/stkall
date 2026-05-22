@extends('layouts.student')
@section('title', $course->title . ' — STKALL')

@section('content')
<div class="py-10">
    {{-- Header --}}
    <div class="text-center mb-10">
        <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $course->title }}</h1>
        @if($course->description)
            <p class="text-gray-500 max-w-lg mx-auto">{{ $course->description }}</p>
        @endif
        <div class="flex items-center justify-center gap-4 mt-4 text-sm text-gray-500">
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{ $course->materials->count() }} Materi
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ $course->teacher->name }}
            </span>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Form Mulai Belajar --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 text-lg mb-1">Mulai Belajar</h2>
            <p class="text-sm text-gray-500 mb-5">Masukkan nama dan kelas untuk memulai pembelajaran.</p>

            <form method="POST" action="{{ route('student.course.join', $course->slug) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <input type="text" name="class" value="{{ old('class') }}" placeholder="Contoh: X IPA 1"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('class') border-red-400 @enderror">
                    @error('class')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Mulai Sekarang
                </button>
            </form>
        </div>

        {{-- Form Lanjutkan Sesi --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 text-lg mb-1">Lanjutkan Sesi</h2>
            <p class="text-sm text-gray-500 mb-5">Sudah pernah masuk? Gunakan token sesi Anda.</p>

            <form method="POST" action="{{ route('student.course.resume', $course->slug) }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Token Sesi</label>
                    <input type="text" name="session_token" placeholder="Contoh: ABCDEF123456"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm font-mono tracking-widest focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition uppercase @error('session_token') border-red-400 @enderror"
                        style="text-transform:uppercase">
                    @error('session_token')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Token sesi diberikan saat pertama kali bergabung.</p>
                </div>
                <button type="submit"
                    class="w-full bg-gray-700 hover:bg-gray-800 text-white font-semibold py-2.5 rounded-lg transition text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Lanjutkan
                </button>
            </form>
        </div>
    </div>

    {{-- Daftar Materi --}}
    @if($course->materials->count())
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-bold text-gray-900 text-lg mb-4">Daftar Materi Pembelajaran</h2>
        <div class="space-y-3">
            @foreach($course->materials as $index => $material)
            <div class="flex items-center gap-4 p-3 rounded-xl {{ $index === 0 ? 'bg-blue-50 border border-blue-100' : 'bg-gray-50' }}">
                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                    {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }} font-bold text-sm">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 text-sm truncate">{{ $material->title }}</p>
                    <div class="flex gap-2 mt-0.5">
                        @if($material->content)
                            <span class="text-xs text-gray-400">📄 Teks</span>
                        @endif
                        @if($material->video_url)
                            <span class="text-xs text-gray-400">🎬 Video</span>
                        @endif
                        @if($material->assessment)
                            <span class="text-xs text-gray-400">📝 Asesmen</span>
                        @endif
                    </div>
                </div>
                @if($index === 0)
                    <span class="text-xs bg-blue-100 text-blue-700 font-medium px-2 py-1 rounded-full">Mulai di sini</span>
                @else
                    <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
