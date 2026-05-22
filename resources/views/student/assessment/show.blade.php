@extends('layouts.student')
@section('title', 'Asesmen — ' . $assessment->title)

@section('content')
<div class="py-6 max-w-2xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('student.material.show', ['slug' => $course->slug, 'materialId' => $assessment->material_id]) }}"
            class="hover:text-blue-600 transition flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Materi
        </a>
    </div>

    @if($existingAnswer)
    {{-- Sudah mengerjakan --}}
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
                    <p class="text-gray-300 text-xs">Jawaban sudah dikirim</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            {{-- Status --}}
            @if($existingAnswer->status === 'graded')
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-green-800">Sudah Dinilai ✅</p>
                        @if($existingAnswer->teacher_feedback)
                            <p class="text-sm text-green-700 mt-1"><strong>Feedback guru:</strong> {{ $existingAnswer->teacher_feedback }}</p>
                        @endif
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-green-700">{{ $existingAnswer->score }}</p>
                        <p class="text-xs text-green-600">/ 100</p>
                    </div>
                </div>
            </div>

            {{-- Token materi berikutnya --}}
            @php
                $nextMaterialToken = $student->tokens()
                    ->where('type', 'material')
                    ->where('is_used', false)
                    ->latest()
                    ->first();
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
                    <div>
                        <p class="font-semibold text-yellow-800">Menunggu Penilaian ⏳</p>
                        <p class="text-sm text-yellow-700">Jawaban Anda sedang ditinjau oleh guru. Token materi berikutnya akan diberikan setelah penilaian selesai.</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Tampilkan jawaban yang sudah dikirim --}}
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Jawaban yang Anda kirim:</p>
                <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700 leading-relaxed">
                    {{ $existingAnswer->answer }}
                </div>
                <p class="text-xs text-gray-400 mt-2">Dikirim {{ $existingAnswer->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    @else
    {{-- Form pengerjaan asesmen --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-5 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-lg">{{ $assessment->title }}</h1>
                    <p class="text-yellow-100 text-xs">{{ $assessment->material->title }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            {{-- Info siswa --}}
            <div class="bg-gray-50 rounded-xl p-3 mb-5 flex items-center gap-3 text-sm text-gray-600">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                <span><strong>{{ $student->name }}</strong> · {{ $student->class }}</span>
            </div>

            {{-- Soal --}}
            @if($assessment->instructions)
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 mb-5">
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

            {{-- Peringatan --}}
            <div class="bg-red-50 border border-red-100 rounded-xl p-3 mb-5 flex items-start gap-2">
                <svg class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p class="text-xs text-red-600">Jawaban hanya bisa dikirim <strong>satu kali</strong>. Pastikan jawaban Anda sudah lengkap sebelum mengirim.</p>
            </div>

            {{-- Form jawaban --}}
            <form method="POST"
                action="{{ route('student.assessment.store', ['slug' => $course->slug, 'assessmentId' => $assessment->id]) }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jawaban Anda <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        name="answer"
                        rows="8"
                        placeholder="Tulis jawaban essay Anda di sini..."
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 outline-none transition resize-y @error('answer') border-red-400 @enderror"
                    >{{ old('answer') }}</textarea>
                    @error('answer')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-between mt-1">
                        <p class="text-xs text-gray-400">Minimal 30 karakter</p>
                        <p class="text-xs text-gray-400" id="charCount">0 karakter</p>
                    </div>
                </div>

                <button type="submit"
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
    const ta = document.querySelector('textarea[name="answer"]');
    if (ta) {
        const counter = document.getElementById('charCount');
        function updateCount() {
            const len = ta.value.length;
            counter.textContent = len + ' karakter';
            counter.className = 'text-xs ' + (len < 30 ? 'text-red-400' : 'text-green-600');
        }
        ta.addEventListener('input', updateCount);
        updateCount();
    }
</script>
@endsection
