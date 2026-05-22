@extends('layouts.student')
@section('title', 'Tulis Resume — ' . $material->title)

@section('content')
<div class="py-6 max-w-2xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('student.material.show', ['slug' => $course->slug, 'materialId' => $material->id]) }}"
            class="hover:text-blue-600 transition flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Materi
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 text-white">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-lg">Tulis Resume</h1>
                    <p class="text-blue-100 text-xs">{{ $material->title }}</p>
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

            {{-- Jika sudah pernah submit tapi ditolak --}}
            @if($existingResume && $existingResume->status === 'rejected')
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                <p class="text-sm font-semibold text-red-800 mb-1">Resume sebelumnya ditolak</p>
                @if($existingResume->teacher_feedback)
                    <p class="text-sm text-red-700"><strong>Catatan guru:</strong> {{ $existingResume->teacher_feedback }}</p>
                @endif
                <p class="text-xs text-red-600 mt-2">Silakan perbaiki dan kirim ulang.</p>
            </div>
            @endif

            {{-- Panduan --}}
            <div class="bg-blue-50 rounded-xl p-4 mb-5">
                <p class="text-sm font-semibold text-blue-800 mb-2">💡 Panduan Menulis Resume</p>
                <ul class="text-xs text-blue-700 space-y-1 list-disc list-inside">
                    <li>Tuliskan poin-poin penting dari materi yang telah dipelajari</li>
                    <li>Gunakan kata-kata sendiri, bukan menyalin langsung</li>
                    <li>Minimal 50 karakter (semakin detail semakin baik)</li>
                    <li>Resume akan ditinjau oleh guru sebelum Anda mendapat token asesmen</li>
                </ul>
            </div>

            {{-- Form --}}
            <form method="POST"
                action="{{ route('student.resume.store', ['slug' => $course->slug, 'materialId' => $material->id]) }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Isi Resume <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        name="content"
                        rows="10"
                        placeholder="Tuliskan ringkasan/resume Anda di sini..."
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition resize-y @error('content') border-red-400 @enderror"
                    >{{ old('content', $existingResume?->content) }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-between mt-1">
                        <p class="text-xs text-gray-400">Minimal 50 karakter</p>
                        <p class="text-xs text-gray-400" id="charCount">0 karakter</p>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Kirim Resume
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const ta = document.querySelector('textarea[name="content"]');
    const counter = document.getElementById('charCount');
    function updateCount() {
        const len = ta.value.length;
        counter.textContent = len + ' karakter';
        counter.className = 'text-xs ' + (len < 50 ? 'text-red-400' : 'text-green-600');
    }
    ta.addEventListener('input', updateCount);
    updateCount();
</script>
@endsection
