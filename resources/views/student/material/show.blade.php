@extends('layouts.student')
@section('title', $material->title . ' — ' . $course->title)

@section('content')
<div class="py-6">

    {{-- Breadcrumb & Progres --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-3 flex-wrap">
            <a href="{{ route('student.course.show', $course->slug) }}" class="hover:text-blue-600 transition">{{ $course->title }}</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 font-medium">{{ $material->title }}</span>
        </div>

        {{-- Progress bar --}}
        @php
            $totalMaterials = $allMaterials->count();
            $currentIndex   = $allMaterials->search(fn($m) => $m->id === $material->id);
            $progressPct    = $totalMaterials > 0 ? round((($currentIndex + 1) / $totalMaterials) * 100) : 0;
        @endphp
        <div class="flex items-center gap-3">
            <div class="flex-1 bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-500"
                    style="width: {{ $progressPct }}%"></div>
            </div>
            <span class="text-xs text-gray-500 flex-shrink-0">{{ $currentIndex + 1 }} / {{ $totalMaterials }}</span>
        </div>
    </div>

    {{-- Token sesi siswa --}}
    <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 mb-6 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-sm text-blue-700">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            <span><strong>Token Sesi Anda:</strong> <code class="font-mono tracking-widest">{{ $student->session_token }}</code></span>
        </div>
        <span class="text-xs text-blue-500">Simpan untuk lanjutkan belajar!</span>
    </div>

    {{-- Konten Materi --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="border-b border-gray-100 px-6 py-4">
            <h1 class="text-xl font-bold text-gray-900">{{ $material->title }}</h1>
        </div>

        {{-- Video YouTube --}}
        @if($material->youtube_id)
        <div class="px-6 pt-5">
            <div class="rounded-xl overflow-hidden aspect-video bg-black">
                <iframe
                    src="https://www.youtube.com/embed/{{ $material->youtube_id }}"
                    class="w-full h-full"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
        @endif

        {{-- Konten Teks --}}
        @if($material->content)
        <div class="px-6 py-5 prose max-w-none text-gray-700 text-sm leading-relaxed">
            {!! $material->content !!}
        </div>
        @endif
    </div>

    {{-- Panel Status Resume --}}
    @if($hasResume)
        @if($resumeStatus === 'approved')
            <div class="bg-green-50 border border-green-200 rounded-2xl p-5 mb-6">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-green-800">Resume Disetujui! ✅</p>
                        <p class="text-sm text-green-700 mt-1">Resume Anda sudah disetujui oleh guru. Cek token asesmen di bawah ini.</p>
                    </div>
                </div>
            </div>

            {{-- Tombol ke Asesmen (pakai token) --}}
            @if($material->assessment)
            @php
                $assessmentToken = $student->tokens()
                    ->where('type', 'assessment')
                    ->where('material_id', $material->id)
                    ->where('is_used', false)
                    ->latest()
                    ->first();
            @endphp
            @if($assessmentToken)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="font-bold text-gray-900 mb-1">Token Asesmen Tersedia! 🎉</h3>
                <p class="text-sm text-gray-500 mb-4">Gunakan token berikut untuk mengerjakan asesmen:</p>
                <div class="bg-yellow-50 border-2 border-dashed border-yellow-300 rounded-xl p-4 text-center mb-4">
                    <p class="text-3xl font-bold font-mono tracking-[0.3em] text-yellow-700">{{ $assessmentToken->token }}</p>
                    <p class="text-xs text-yellow-600 mt-1">Berlaku sampai {{ $assessmentToken->expires_at?->format('d M Y H:i') }}</p>
                </div>
                <a href="{{ route('student.token.gate', ['slug' => $course->slug, 'type' => 'assessment', 'materialId' => $material->id]) }}"
                    class="block w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2.5 rounded-lg text-center text-sm transition">
                    Kerjakan Asesmen →
                </a>
            </div>
            @else
            <div class="bg-gray-50 rounded-2xl border border-gray-200 p-5 mb-6 text-center">
                <p class="text-gray-500 text-sm">Asesmen sudah dikerjakan. Tunggu nilai dari guru.</p>
            </div>
            @endif
            @endif

        @elseif($resumeStatus === 'pending')
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 mb-6">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-yellow-800">Resume Menunggu Persetujuan ⏳</p>
                        <p class="text-sm text-yellow-700 mt-1">Resume Anda sudah dikirim. Tunggu persetujuan dari guru untuk mendapatkan token asesmen.</p>
                    </div>
                </div>
            </div>

        @elseif($resumeStatus === 'rejected')
            @php
                $rejectedResume = $student->resumes()->where('material_id', $material->id)->first();
            @endphp
            <div class="bg-red-50 border border-red-200 rounded-2xl p-5 mb-6">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-red-800">Resume Ditolak ❌</p>
                        @if($rejectedResume?->teacher_feedback)
                            <p class="text-sm text-red-700 mt-1"><strong>Catatan Guru:</strong> {{ $rejectedResume->teacher_feedback }}</p>
                        @endif
                        <a href="{{ route('student.resume.create', ['slug' => $course->slug, 'materialId' => $material->id]) }}"
                            class="inline-block mt-3 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                            Kirim Ulang Resume
                        </a>
                    </div>
                </div>
            </div>
        @endif

    @else
        {{-- Belum ada resume: tampilkan tombol buat resume --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-900 mb-1">Buat Resume Materi</h3>
                    <p class="text-sm text-gray-500 mb-4">Setelah mempelajari materi di atas, tuliskan resume/ringkasan pemahaman Anda. Resume akan ditinjau guru dan Anda mendapat token untuk mengerjakan asesmen.</p>
                    <a href="{{ route('student.resume.create', ['slug' => $course->slug, 'materialId' => $material->id]) }}"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Tulis Resume
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Navigasi Materi --}}
    <div class="grid grid-cols-{{ $allMaterials->count() }} gap-2">
        @foreach($allMaterials as $idx => $m)
        <div class="text-center">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold mx-auto
                {{ $m->id === $material->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                {{ $idx + 1 }}
            </div>
            <p class="text-xs text-gray-400 mt-1 truncate">{{ Str::limit($m->title, 12) }}</p>
        </div>
        @endforeach
    </div>

</div>
@endsection
