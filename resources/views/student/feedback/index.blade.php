@extends('layouts.student')
@section('title', 'Nilai & Feedback — ' . $course->title)

@section('content')
<div class="py-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Nilai & Feedback</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $course->title }} · {{ $student->name }}</p>
        </div>
        <a href="{{ route('student.course.show', $course->slug) }}"
            class="text-sm text-blue-600 hover:underline flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Ringkasan --}}
    @php
        $gradedCount   = $assessmentAnswers->where('status', 'graded')->count();
        $pendingCount  = $assessmentAnswers->where('status', 'pending')->count();
        $avgScore      = $gradedCount ? round($assessmentAnswers->where('status','graded')->avg('score')) : null;
        $approvedCount = $resumes->where('status', 'approved')->count();
        $rejectedCount = $resumes->where('status', 'rejected')->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $avgScore ?? '—' }}</p>
            <p class="text-xs text-gray-500 mt-1">Rata-rata Nilai</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $gradedCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Asesmen Dinilai</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $approvedCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Resume Disetujui</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-yellow-500">{{ $pendingCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Menunggu Nilai</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- BAGIAN ASESMEN --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <h2 class="font-bold text-gray-800 text-base mb-3 flex items-center gap-2">
        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Jawaban Asesmen
    </h2>

    @forelse($assessmentAnswers as $answer)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden">

        {{-- Header kartu --}}
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100
            {{ $answer->status === 'graded' ? 'bg-green-50' : 'bg-yellow-50' }}">
            <div>
                <p class="font-semibold text-gray-800 text-sm">{{ $answer->assessment->title }}</p>
                <p class="text-xs text-gray-500">{{ $answer->assessment->material->title }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if($answer->status === 'graded')
                    <div class="text-center">
                        <p class="text-2xl font-bold {{ $answer->score >= 75 ? 'text-green-600' : ($answer->score >= 60 ? 'text-yellow-600' : 'text-red-500') }}">
                            {{ $answer->score }}
                        </p>
                        <p class="text-xs text-gray-400">/ 100</p>
                    </div>
                    <span class="bg-green-100 text-green-700 text-xs font-medium px-2.5 py-1 rounded-full">Sudah Dinilai</span>
                @else
                    <span class="bg-yellow-100 text-yellow-700 text-xs font-medium px-2.5 py-1 rounded-full">Menunggu Nilai</span>
                @endif
            </div>
        </div>

        <div class="p-5 space-y-4">

            {{-- Soal --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Soal</p>
                <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">{{ $answer->assessment->instructions }}</p>
            </div>

            {{-- Jawaban Siswa --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Jawaban Anda</p>
                <p class="text-sm text-gray-700 bg-blue-50 rounded-lg p-3 leading-relaxed">{{ $answer->answer }}</p>
                <p class="text-xs text-gray-400 mt-1">Dikirim {{ $answer->created_at->format('d M Y H:i') }}</p>
            </div>

            {{-- Feedback Guru --}}
            @if($answer->status === 'graded')
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">💬 Feedback Guru</p>
                @if($answer->teacher_feedback)
                    {{-- Percakapan: Guru --}}
                    <div class="flex items-start gap-3 mb-3">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">G</div>
                        <div class="flex-1">
                            <div class="bg-blue-50 border border-blue-100 rounded-2xl rounded-tl-none px-4 py-2.5">
                                <p class="text-sm text-gray-800">{{ $answer->teacher_feedback }}</p>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Guru · {{ $answer->graded_at?->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    {{-- Balasan Siswa (jika ada) --}}
                    @if($answer->student_reply)
                    <div class="flex items-start gap-3 flex-row-reverse mb-3">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">
                            {{ strtoupper(substr($student->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 text-right">
                            <div class="bg-green-50 border border-green-100 rounded-2xl rounded-tr-none px-4 py-2.5 inline-block text-left">
                                <p class="text-sm text-gray-800">{{ $answer->student_reply }}</p>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Anda · {{ $answer->student_replied_at?->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Form Balas --}}
                    @if(!$answer->student_reply)
                    <form method="POST"
                        action="{{ route('student.feedback.reply.assessment', ['slug' => $course->slug, 'answerId' => $answer->id]) }}">
                        @csrf
                        <div class="flex gap-2 mt-2">
                            <input type="text" name="student_reply"
                                placeholder="Tulis balasan Anda..."
                                maxlength="1000"
                                class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:border-green-400 outline-none transition @error('student_reply') border-red-400 @enderror">
                            <button type="submit"
                                class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-xl transition flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Balas
                            </button>
                        </div>
                        @error('student_reply')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </form>
                    @else
                    <p class="text-xs text-gray-400 italic mt-1">✓ Anda sudah membalas feedback ini.</p>
                    @endif

                @else
                    <p class="text-sm text-gray-400 italic">Guru belum memberikan feedback tertulis.</p>
                @endif
            </div>
            @endif

        </div>
    </div>
    @empty
    <div class="bg-gray-50 rounded-2xl p-8 text-center mb-6">
        <p class="text-gray-400 text-sm">Belum ada jawaban asesmen.</p>
    </div>
    @endforelse

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- BAGIAN RESUME --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <h2 class="font-bold text-gray-800 text-base mb-3 mt-6 flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Resume Materi
    </h2>

    @forelse($resumes as $resume)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden">

        {{-- Header kartu --}}
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100
            {{ $resume->status === 'approved' ? 'bg-green-50' : ($resume->status === 'rejected' ? 'bg-red-50' : 'bg-yellow-50') }}">
            <div>
                <p class="font-semibold text-gray-800 text-sm">{{ $resume->material->title }}</p>
                <p class="text-xs text-gray-500">Resume Materi</p>
            </div>
            @php
                $badgeColor = match($resume->status) {
                    'approved' => 'bg-green-100 text-green-700',
                    'rejected' => 'bg-red-100 text-red-700',
                    default    => 'bg-yellow-100 text-yellow-700',
                };
                $badgeText = match($resume->status) {
                    'approved' => 'Disetujui ✓',
                    'rejected' => 'Ditolak ✗',
                    default    => 'Menunggu',
                };
            @endphp
            <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badgeColor }}">{{ $badgeText }}</span>
        </div>

        <div class="p-5 space-y-4">

            {{-- Isi Resume --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Resume Anda</p>

                {{-- Teks resume (jika ada) --}}
                @if($resume->content)
                <p class="text-sm text-gray-700 bg-blue-50 rounded-lg p-3 leading-relaxed mb-2">
                    {{ \Illuminate\Support\Str::limit($resume->content, 300) }}
                </p>
                @endif

                {{-- File upload (jika ada) --}}
                @if($resume->hasFile())
                <div class="flex items-center gap-3 bg-purple-50 border border-purple-200 rounded-xl p-3 mb-2">
                    <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        @if($resume->isImage())
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-purple-800 truncate">📎 {{ $resume->file_name }}</p>
                        <p class="text-xs text-purple-600 uppercase">{{ $resume->file_type }}</p>
                    </div>
                    <a href="{{ $resume->file_url }}" target="_blank"
                        class="text-xs bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-lg transition flex-shrink-0">
                        {{ $resume->isImage() ? 'Lihat' : 'Unduh' }}
                    </a>
                </div>
                @endif

                {{-- Video YouTube (jika ada) --}}
                @if($resume->video_url)
                <div class="bg-red-50 border border-red-200 rounded-xl overflow-hidden mb-2">
                    @if($resume->youtube_id)
                    <div class="aspect-video">
                        <iframe src="https://www.youtube.com/embed/{{ $resume->youtube_id }}"
                            class="w-full h-full" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    </div>
                    @else
                    <div class="flex items-center gap-3 p-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                        <a href="{{ $resume->video_url }}" target="_blank"
                            class="text-sm text-red-700 underline hover:text-red-900 truncate">
                            {{ $resume->video_url }}
                        </a>
                    </div>
                    @endif
                </div>
                @endif

                @if(!$resume->content && !$resume->hasFile() && !$resume->video_url)
                <p class="text-xs text-gray-400 italic">Tidak ada konten resume.</p>
                @endif

                <p class="text-xs text-gray-400 mt-1">Dikirim {{ $resume->created_at->format('d M Y H:i') }}</p>
            </div>

            {{-- Feedback Guru --}}
            @if($resume->status !== 'pending' && $resume->teacher_feedback)
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">💬 Feedback Guru</p>

                {{-- Percakapan: Guru --}}
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">G</div>
                    <div class="flex-1">
                        <div class="bg-blue-50 border border-blue-100 rounded-2xl rounded-tl-none px-4 py-2.5">
                            <p class="text-sm text-gray-800">{{ $resume->teacher_feedback }}</p>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Guru · {{ $resume->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                {{-- Balasan Siswa (jika ada) --}}
                @if($resume->student_reply)
                <div class="flex items-start gap-3 flex-row-reverse mb-3">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 text-right">
                        <div class="bg-green-50 border border-green-100 rounded-2xl rounded-tr-none px-4 py-2.5 inline-block text-left">
                            <p class="text-sm text-gray-800">{{ $resume->student_reply }}</p>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Anda · {{ $resume->student_replied_at?->format('d M Y H:i') }}</p>
                    </div>
                </div>
                @endif

                {{-- Form Balas --}}
                @if(!$resume->student_reply)
                <form method="POST"
                    action="{{ route('student.feedback.reply.resume', ['slug' => $course->slug, 'resumeId' => $resume->id]) }}">
                    @csrf
                    <div class="flex gap-2 mt-2">
                        <input type="text" name="student_reply"
                            placeholder="Tulis balasan Anda..."
                            maxlength="1000"
                            class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:border-green-400 outline-none transition @error('student_reply') border-red-400 @enderror">
                        <button type="submit"
                            class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-xl transition flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Balas
                        </button>
                    </div>
                    @error('student_reply')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </form>
                @else
                <p class="text-xs text-gray-400 italic mt-1">✓ Anda sudah membalas feedback ini.</p>
                @endif
            </div>

            @elseif($resume->status === 'pending')
            <div class="border-t border-gray-100 pt-3">
                <p class="text-sm text-gray-400 italic text-center">Menunggu review dari guru...</p>
            </div>
            @endif

        </div>
    </div>
    @empty
    <div class="bg-gray-50 rounded-2xl p-8 text-center">
        <p class="text-gray-400 text-sm">Belum ada resume yang dikirim.</p>
    </div>
    @endforelse

</div>
@endsection
