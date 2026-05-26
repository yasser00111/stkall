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
            <div class="flex items-center gap-3">
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

            {{-- Jika resume ditolak --}}
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
            <div class="bg-blue-50 rounded-xl p-4 mb-6">
                <p class="text-sm font-semibold text-blue-800 mb-2">💡 Panduan Membuat Resume</p>
                <ul class="text-xs text-blue-700 space-y-1 list-disc list-inside">
                    <li>Isi <strong>minimal satu</strong> dari: teks resume, file upload, atau video</li>
                    <li>File yang diterima: PDF, Word (doc/docx), atau gambar (jpg, png)</li>
                    <li>Ukuran file maksimal <strong>10 MB</strong></li>
                    <li>Video: tempel link YouTube dari video resume Anda</li>
                    <li>Resume akan ditinjau guru sebelum Anda mendapat token asesmen</li>
                </ul>
            </div>

            <form method="POST"
                action="{{ route('student.resume.store', ['slug' => $course->slug, 'materialId' => $material->id]) }}"
                enctype="multipart/form-data">
                @csrf

                {{-- ── BAGIAN 1: Teks Resume ─────────────────────────────── --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">1</div>
                        <label class="text-sm font-semibold text-gray-700">Teks Resume</label>
                        <span class="text-xs text-gray-400">(opsional jika ada file/video)</span>
                    </div>
                    <textarea
                        name="content"
                        id="content"
                        rows="7"
                        placeholder="Tuliskan ringkasan/resume materi di sini..."
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition resize-y @error('content') border-red-400 @enderror"
                    >{{ old('content', $existingResume?->content) }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-between mt-1">
                        <p class="text-xs text-gray-400">Tuliskan pemahaman Anda dengan kata-kata sendiri</p>
                        <p class="text-xs text-gray-400" id="charCount">0 karakter</p>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="flex items-center gap-3 my-5">
                    <div class="flex-1 border-t border-gray-200"></div>
                    <span class="text-xs text-gray-400 font-medium">ATAU TAMBAHKAN</span>
                    <div class="flex-1 border-t border-gray-200"></div>
                </div>

                {{-- ── BAGIAN 2: Upload File ─────────────────────────────── --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 bg-purple-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">2</div>
                        <label class="text-sm font-semibold text-gray-700">Upload File</label>
                        <span class="text-xs text-gray-400">(PDF, Word, atau Gambar — max 10 MB)</span>
                    </div>

                    {{-- Preview file yang sudah ada --}}
                    @if($existingResume && $existingResume->file_path)
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-3 mb-3 flex items-center gap-3">
                        @if($existingResume->isImage())
                            <img src="{{ $existingResume->file_url }}"
                                class="w-12 h-12 object-cover rounded-lg border border-purple-300" alt="preview">
                        @else
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-purple-800 truncate">{{ $existingResume->file_name }}</p>
                            <p class="text-xs text-purple-600">File sebelumnya — akan diganti jika upload baru</p>
                        </div>
                        <a href="{{ $existingResume->file_url }}" target="_blank"
                            class="text-xs text-purple-700 underline flex-shrink-0 hover:text-purple-900">
                            Lihat
                        </a>
                    </div>
                    @endif

                    {{-- Dropzone upload --}}
                    <label for="file"
                        class="flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-xl p-6 cursor-pointer hover:border-purple-400 hover:bg-purple-50 transition group @error('file') border-red-400 @enderror"
                        id="dropzone-label">
                        <div class="w-12 h-12 bg-gray-100 group-hover:bg-purple-100 rounded-xl flex items-center justify-center mb-3 transition">
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-purple-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 group-hover:text-purple-700 font-medium transition" id="dropzone-text">
                            Klik untuk pilih file atau drag & drop di sini
                        </p>
                        <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, JPG, PNG — Maks. 10 MB</p>
                        <input type="file" id="file" name="file" class="hidden"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp">
                    </label>
                    @error('file')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    {{-- Preview file baru yang dipilih --}}
                    <div id="file-preview" class="hidden mt-3 bg-green-50 border border-green-200 rounded-xl p-3 flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-green-800 truncate" id="file-name-preview">—</p>
                            <p class="text-xs text-green-600" id="file-size-preview">—</p>
                        </div>
                        <button type="button" id="remove-file"
                            class="text-xs text-red-500 hover:text-red-700 flex-shrink-0">
                            Hapus
                        </button>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="flex items-center gap-3 my-5">
                    <div class="flex-1 border-t border-gray-200"></div>
                    <span class="text-xs text-gray-400 font-medium">ATAU TAMBAHKAN</span>
                    <div class="flex-1 border-t border-gray-200"></div>
                </div>

                {{-- ── BAGIAN 3: Link Video YouTube ──────────────────────── --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">3</div>
                        <label for="video_url" class="text-sm font-semibold text-gray-700">Link Video YouTube</label>
                        <span class="text-xs text-gray-400">(opsional)</span>
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1 relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2">
                                <svg class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </div>
                            <input type="text" name="video_url" id="video_url"
                                value="{{ old('video_url', $existingResume?->video_url) }}"
                                placeholder="https://www.youtube.com/watch?v=..."
                                class="w-full border border-gray-300 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 outline-none transition @error('video_url') border-red-400 @enderror">
                        </div>
                        {{-- Tombol preview video --}}
                        <button type="button" id="preview-video-btn"
                            class="hidden bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium px-3 py-2.5 rounded-xl transition flex-shrink-0">
                            ▶ Preview
                        </button>
                    </div>
                    @error('video_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Rekam video resume Anda, upload ke YouTube (boleh private), lalu tempel link-nya di sini</p>

                    {{-- Preview embed video --}}
                    <div id="video-preview" class="hidden mt-3 rounded-xl overflow-hidden aspect-video bg-black">
                        <iframe id="video-iframe" src="" class="w-full h-full" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    </div>
                </div>

                {{-- Error minimal 1 field --}}
                @error('content')
                    @if(str_contains($message, 'minimal salah satu'))
                    <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-red-700">{{ $message }}</p>
                    </div>
                    @endif
                @enderror

                {{-- Tombol submit --}}
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2 mt-2">
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
// ── Counter karakter teks ──────────────────────────────────────────────
const textarea = document.getElementById('content');
const counter  = document.getElementById('charCount');
if (textarea) {
    function updateCount() {
        const len = textarea.value.length;
        counter.textContent = len + ' karakter';
        counter.className   = 'text-xs ' + (len === 0 ? 'text-gray-400' : 'text-green-600');
    }
    textarea.addEventListener('input', updateCount);
    updateCount();
}

// ── Upload file preview ───────────────────────────────────────────────
const fileInput    = document.getElementById('file');
const filePreview  = document.getElementById('file-preview');
const fileNamePrev = document.getElementById('file-name-preview');
const fileSizePrev = document.getElementById('file-size-preview');
const dropzoneText = document.getElementById('dropzone-text');
const removeBtn    = document.getElementById('remove-file');

function formatBytes(bytes) {
    if (bytes < 1024)       return bytes + ' B';
    if (bytes < 1048576)    return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

fileInput.addEventListener('change', function () {
    if (this.files.length > 0) {
        const file = this.files[0];
        fileNamePrev.textContent = file.name;
        fileSizePrev.textContent = formatBytes(file.size);
        filePreview.classList.remove('hidden');
        dropzoneText.textContent = '✓ File dipilih: ' + file.name;
    }
});

removeBtn.addEventListener('click', function () {
    fileInput.value = '';
    filePreview.classList.add('hidden');
    dropzoneText.textContent = 'Klik untuk pilih file atau drag & drop di sini';
});

// Drag & drop
const dropLabel = document.getElementById('dropzone-label');
['dragenter','dragover'].forEach(e => {
    dropLabel.addEventListener(e, ev => {
        ev.preventDefault();
        dropLabel.classList.add('border-purple-500', 'bg-purple-50');
    });
});
['dragleave','drop'].forEach(e => {
    dropLabel.addEventListener(e, ev => {
        ev.preventDefault();
        dropLabel.classList.remove('border-purple-500', 'bg-purple-50');
    });
});
dropLabel.addEventListener('drop', function (ev) {
    if (ev.dataTransfer.files.length > 0) {
        fileInput.files = ev.dataTransfer.files;
        fileInput.dispatchEvent(new Event('change'));
    }
});

// ── Preview video YouTube ─────────────────────────────────────────────
const videoInput   = document.getElementById('video_url');
const previewBtn   = document.getElementById('preview-video-btn');
const videoPreview = document.getElementById('video-preview');
const videoIframe  = document.getElementById('video-iframe');

function extractYoutubeId(url) {
    const match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
    return match ? match[1] : null;
}

function updateVideoPreview() {
    const url = videoInput.value.trim();
    const id  = extractYoutubeId(url);
    if (id) {
        previewBtn.classList.remove('hidden');
    } else {
        previewBtn.classList.add('hidden');
        videoPreview.classList.add('hidden');
    }
}

videoInput.addEventListener('input', updateVideoPreview);
updateVideoPreview();

previewBtn.addEventListener('click', function () {
    const id = extractYoutubeId(videoInput.value.trim());
    if (id) {
        videoIframe.src = 'https://www.youtube.com/embed/' + id;
        videoPreview.classList.toggle('hidden');
        this.textContent = videoPreview.classList.contains('hidden') ? '▶ Preview' : '✕ Tutup';
    }
});

// Jika sudah ada video_url dari existing resume, tampilkan preview btn
@if($existingResume?->video_url)
    updateVideoPreview();
@endif
</script>
@endsection
