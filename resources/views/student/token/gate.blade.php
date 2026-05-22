@extends('layouts.student')
@section('title', 'Masukkan Token — STKALL')

@section('content')
<div class="py-10 max-w-md mx-auto">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r {{ $type === 'assessment' ? 'from-yellow-500 to-orange-500' : 'from-green-500 to-emerald-600' }} px-6 py-6 text-white text-center">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                @if($type === 'assessment')
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                @else
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                @endif
            </div>
            <h1 class="font-bold text-xl">
                {{ $type === 'assessment' ? 'Token Asesmen' : 'Token Materi' }}
            </h1>
            <p class="text-sm opacity-80 mt-1">
                {{ $type === 'assessment' ? 'Masukkan token untuk mengerjakan asesmen' : 'Masukkan token untuk membuka materi berikutnya' }}
            </p>
        </div>

        <div class="p-6">
            {{-- Info materi --}}
            <div class="bg-gray-50 rounded-xl p-4 mb-6 text-sm text-gray-600">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800">{{ $material->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $course->title }}</p>
                    </div>
                </div>
            </div>

            {{-- Form token --}}
            <form method="POST"
                action="{{ route('student.token.verify', ['slug' => $course->slug, 'type' => $type, 'materialId' => $material->id]) }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2 text-center">
                        Masukkan Kode Token (8 karakter)
                    </label>
                    <input
                        type="text"
                        name="token"
                        maxlength="8"
                        placeholder="XXXXXXXX"
                        autocomplete="off"
                        style="text-transform:uppercase; letter-spacing:0.3em"
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-4 text-2xl font-mono text-center tracking-widest uppercase focus:ring-2 {{ $type === 'assessment' ? 'focus:ring-yellow-400 focus:border-yellow-400' : 'focus:ring-green-400 focus:border-green-400' }} outline-none transition @error('token') border-red-400 @enderror"
                    >
                    @error('token')
                        <p class="text-red-500 text-xs mt-2 text-center">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full {{ $type === 'assessment' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-600 hover:bg-green-700' }} text-white font-bold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                    Verifikasi Token
                </button>
            </form>

            <div class="mt-5 pt-5 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-400">Token diberikan oleh guru melalui sistem setelah</p>
                <p class="text-xs text-gray-400">
                    {{ $type === 'assessment' ? 'resume Anda disetujui' : 'jawaban asesmen Anda dinilai' }}
                </p>
                <a href="{{ route('student.material.show', ['slug' => $course->slug, 'materialId' => $material->id]) }}"
                    class="inline-block mt-3 text-xs text-blue-600 hover:underline">← Kembali ke materi</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const input = document.querySelector('input[name="token"]');
    input.addEventListener('input', function () {
        this.value = this.value.toUpperCase();
    });
    input.focus();
</script>
@endsection
