@extends('layouts.student')
@section('title', 'Asesmen — ' . $assessment->title)

@section('content')
<div class="py-6 max-w-2xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('student.material.show', ['slug' => $course->slug, 'materialId' => $assessment->material_id]) }}"
            class="text-sm text-gray-500 hover:text-blue-600 transition flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Materi
        </a>
        <a href="{{ route('student.feedback.index', $course->slug) }}"
            class="text-sm bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Nilai & Feedback
        </a>
    </div>


    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- KASUS 1: Deadline sudah lewat & belum pernah jawab --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if(session('deadline_passed') || (!$existingAnswer && $assessment->deadline && $assessment->deadline->isPast()))
    <div class="bg-white rounded-2xl shadow-sm border border-red-200 overflow-hidden">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-5 text-white text-center">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="font-bold text-xl">Deadline Sudah Lewat</h1>
            <p class="text-red-100 text-sm mt-1">{{ $assessment->title }}</p>
        </div>
        <div class="p-6 text-center">
            <p class="text-gray-600 mb-2">Asesmen ini sudah melewati batas waktu pengerjaan.</p>
            <p class="text-sm text-gray-400">
                Deadline: <strong>{{ $assessment->deadline->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</strong>
            </p>
            <a href="{{ route('student.material.show', ['slug' => $course->slug, 'materialId' => $assessment->material_id]) }}"
                class="inline-block mt-5 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition">
                ← Kembali ke Materi
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- KASUS 2: Waktu habis saat mengerjakan --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @elseif(!$existingAnswer && ($timeExpired ?? false))
    <div class="bg-white rounded-2xl shadow-sm border border-orange-200 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-5 text-white text-center">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h1 class="font-bold text-xl">Waktu Habis!</h1>
            <p class="text-orange-100 text-sm mt-1">{{ $assessment->title }}</p>
        </div>
        <div class="p-6 text-center">
            <p class="text-gray-600 mb-2">Batas waktu pengerjaan sudah berakhir.</p>
            <p class="text-sm text-gray-400">Batas waktu: <strong>{{ $assessment->time_limit_label }}</strong></p>
            <a href="{{ route('student.material.show', ['slug' => $course->slug, 'materialId' => $assessment->material_id]) }}"
                class="inline-block mt-5 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition">
                ← Kembali ke Materi
            </a>
        </div>
    </div>



    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- KASUS 3: Sudah mengerjakan — tampilkan hasil & feedback --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @elseif($existingAnswer)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-5 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-lg">{{ $assessment->title }}</h1>
                    <p class="text-gray-300 text-xs">Jawaban sudah dikirim
                        @if($existingAnswer->duration_minutes)
                            · Durasi: {{ $existingAnswer->duration_minutes }} menit
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="p-6">
            {{-- Nilai --}}
            @if($existingAnswer->status === 'graded')
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="font-semibold text-green-800">Sudah Dinilai ✅</p>
                        @if($existingAnswer->teacher_feedback)
                            <p class="text-sm text-green-700 mt-1 leading-relaxed">
                                <strong>Feedback Guru:</strong> {{ $existingAnswer->teacher_feedback }}
                            </p>
                        @endif
                        @if($existingAnswer->student_reply)
                            <div class="mt-2 bg-white rounded-lg border border-green-200 p-2.5">
                                <p class="text-xs text-green-600 font-medium mb-0.5">Balasan Anda:</p>
                                <p class="text-sm text-gray-700">{{ $existingAnswer->student_reply }}</p>
                            </div>
                        @elseif($existingAnswer->teacher_feedback)
                            <a href="{{ route('student.feedback.index', $course->slug) }}"
                                class="inline-block mt-2 text-xs text-green-700 underline hover:text-green-900">
                                Balas feedback guru →
                            </a>
                        @endif
                    </div>
                    <div class="text-center ml-4 flex-shrink-0">
                        <p class="text-4xl font-bold {{ $existingAnswer->score >= 75 ? 'text-green-600' : ($existingAnswer->score >= 60 ? 'text-yellow-600' : 'text-red-500') }}">
                            {{ $existingAnswer->score }}
                        </p>
                        <p class="text-xs text-green-600">/ 100</p>
                    </div>
                </div>
            </div>

            {{-- Token materi berikutnya --}}
            @php
                $nextMaterialToken = $student->tokens()
                    ->where('type', 'material')->where('is_used', false)->latest()->first();
            @endphp
            @if($nextMaterialToken)
            <div class="bg-green-50 border-2 border-dashed border-green-300 rounded-xl p-5 mb-5 text-center">
                <p class="text-sm font-semibold text-green-800 mb-2">🎉 Token Materi Berikutnya!</p>
                <p class="text-3xl font-bold font-mono tracking-[0.3em] text-green-700 mb-1">{{ $nextMaterialToken->token }}</p>
                <p class="text-xs text-green-600">Berlaku sampai {{ $nextMaterialToken->expires_at?->format('d M Y H:i') }}</p>
                <a href="{{ route('student.token.gate', ['slug' => $course->slug, 'type' => 'material', 'materialId' => $nextMaterialToken->material_id]) }}"
                    class="inline-block mt-3 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Lanjut ke Materi Berikutnya →
                </a>
            </div>
            @endif
            @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-5">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-yellow-800 font-medium">Menunggu Penilaian ⏳ — Jawaban Anda sedang ditinjau guru.</p>
                </div>
            </div>
            @endif

            {{-- Jawaban yang dikirim --}}
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Jawaban yang Anda kirim:</p>
                <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700 leading-relaxed">{{ $existingAnswer->answer }}</div>
                <p class="text-xs text-gray-400 mt-2">Dikirim {{ $existingAnswer->created_at->format('d M Y H:i') }}</p>
            </div>

            <div class="mt-5 pt-5 border-t border-gray-100 text-center">
                <a href="{{ route('student.feedback.index', $course->slug) }}"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                    Lihat Semua Nilai & Feedback
                </a>
            </div>
        </div>
    </div>



    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- KASUS 4: Form pengerjaan asesmen (dengan countdown timer) --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-5 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg">{{ $assessment->title }}</h1>
                        <p class="text-yellow-100 text-xs">{{ $assessment->material->title }}</p>
                    </div>
                </div>

                {{-- Countdown Timer (hanya tampil jika ada time_limit) --}}
                @if($assessment->time_limit && isset($remainingSeconds))
                <div id="timer-box"
                    class="flex flex-col items-center bg-white/20 backdrop-blur rounded-xl px-4 py-2 min-w-[90px]">
                    <div id="timer-display" class="text-2xl font-bold font-mono tracking-wider">
                        {{ gmdate('H:i:s', max(0, $remainingSeconds)) }}
                    </div>
                    <p class="text-xs text-yellow-100 mt-0.5">Sisa Waktu</p>
                </div>
                @endif
            </div>
        </div>

        <div class="p-6">
            {{-- Info siswa --}}
            <div class="bg-gray-50 rounded-xl p-3 mb-4 flex items-center justify-between text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <span><strong>{{ $student->name }}</strong> · {{ $student->class }}</span>
                </div>
                {{-- Info batas waktu & deadline --}}
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    @if($assessment->time_limit)
                    <span class="flex items-center gap-1 bg-yellow-50 text-yellow-700 px-2 py-1 rounded-lg font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $assessment->time_limit_label }}
                    </span>
                    @endif
                    @if($assessment->deadline)
                    <span class="flex items-center gap-1 bg-red-50 text-red-600 px-2 py-1 rounded-lg font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Deadline: {{ $assessment->deadline->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                    </span>
                    @endif
                </div>
            </div>



            {{-- Soal --}}
            @if($assessment->instructions)
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 mb-4">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-orange-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-orange-800 mb-1">Soal Asesmen:</p>
                        <p class="text-sm text-orange-900 leading-relaxed">{{ $assessment->instructions }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Peringatan jawaban 1x --}}
            <div class="bg-red-50 border border-red-100 rounded-xl p-3 mb-4 flex items-start gap-2">
                <svg class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p class="text-xs text-red-600">Jawaban hanya bisa dikirim <strong>satu kali</strong>. Pastikan jawaban sudah lengkap sebelum mengirim.</p>
            </div>

            {{-- Form jawaban --}}
            <form id="assessment-form" method="POST"
                action="{{ route('student.assessment.store', ['slug' => $course->slug, 'assessmentId' => $assessment->id]) }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jawaban Anda <span class="text-red-500">*</span>
                    </label>
                    <textarea id="answer-textarea" name="answer" rows="8"
                        placeholder="Tulis jawaban essay Anda di sini..."
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 outline-none transition resize-y @error('answer') border-red-400 @enderror"
                    >{{ old('answer') }}</textarea>
                    @error('answer')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-between mt-1">
                        <p class="text-xs text-gray-400">Minimal 10 karakter</p>
                        <p class="text-xs text-gray-400" id="charCount">0 karakter</p>
                    </div>
                </div>

                <button type="submit" id="submit-btn"
                    onclick="return confirm('Yakin ingin mengirim jawaban? Jawaban tidak dapat diubah setelah dikirim.')"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Kirim Jawaban
                </button>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection



@section('scripts')
<script>
// ── Karakter counter ──────────────────────────────────────────────────
const ta = document.getElementById('answer-textarea');
if (ta) {
    const counter = document.getElementById('charCount');
    function updateCount() {
        const len = ta.value.length;
        counter.textContent = len + ' karakter';
        counter.className = 'text-xs ' + (len < 10 ? 'text-red-400' : 'text-green-600');
    }
    ta.addEventListener('input', updateCount);
    updateCount();
}

// ── Countdown Timer ───────────────────────────────────────────────────
@if(!$existingAnswer && $assessment->time_limit && isset($remainingSeconds) && $remainingSeconds > 0)
(function () {
    let remaining = {{ (int) $remainingSeconds }};
    const display  = document.getElementById('timer-display');
    const timerBox = document.getElementById('timer-box');
    const form     = document.getElementById('assessment-form');
    const submitBtn= document.getElementById('submit-btn');

    function pad(n) { return String(n).padStart(2, '0'); }

    function formatTime(sec) {
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = sec % 60;
        return h > 0
            ? `${pad(h)}:${pad(m)}:${pad(s)}`
            : `${pad(m)}:${pad(s)}`;
    }

    function updateTimer() {
        if (remaining <= 0) {
            clearInterval(interval);
            display.textContent = '00:00';
            timerBox.className = timerBox.className.replace('bg-white/20', 'bg-red-600/80');

            // Nonaktifkan form & submit
            if (ta)        { ta.disabled = true; ta.classList.add('opacity-50'); }
            if (submitBtn) { submitBtn.disabled = true; submitBtn.classList.add('opacity-50', 'cursor-not-allowed'); }

            // Tampilkan notifikasi waktu habis
            const alert = document.createElement('div');
            alert.className = 'fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-red-600 text-white font-bold px-6 py-3 rounded-xl shadow-xl text-sm flex items-center gap-2';
            alert.innerHTML = '⏰ Waktu habis! Anda tidak dapat mengirim jawaban.';
            document.body.appendChild(alert);
            return;
        }

        display.textContent = formatTime(remaining);

        // Ubah warna saat < 5 menit
        if (remaining <= 300 && remaining > 60) {
            timerBox.style.background = 'rgba(234, 88, 12, 0.5)'; // orange
        }
        // Ubah warna & animasi saat < 1 menit
        if (remaining <= 60) {
            timerBox.style.background = 'rgba(220, 38, 38, 0.7)'; // red
            display.classList.add('animate-pulse');
        }

        remaining--;
    }

    updateTimer();
    const interval = setInterval(updateTimer, 1000);

    // Auto-submit saat waktu habis jika ada jawaban
    setTimeout(function () {
        if (ta && ta.value.trim().length >= 10 && form) {
            form.submit();
        }
    }, ({{ (int) $remainingSeconds }} + 1) * 1000);
})();
@endif
</script>
@endsection
