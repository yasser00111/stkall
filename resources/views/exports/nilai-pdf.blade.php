<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1f2937; background: #fff; }

    .header { background: #1d4ed8; color: white; padding: 16px 20px; margin-bottom: 16px; }
    .header h1 { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
    .header p  { font-size: 10px; opacity: 0.85; }
    .meta { font-size: 9px; margin-top: 6px; opacity: 0.7; }

    .stats { display: flex; gap: 12px; margin: 0 20px 16px; }
    .stat-box { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; text-align: center; }
    .stat-box .num { font-size: 20px; font-weight: bold; color: #1d4ed8; }
    .stat-box .lbl { font-size: 8px; color: #6b7280; margin-top: 2px; }

    table { width: 100%; border-collapse: collapse; margin: 0 0 20px; font-size: 9px; }
    thead tr { background: #1d4ed8; color: white; }
    thead th { padding: 7px 6px; text-align: left; font-weight: bold; }
    tbody tr:nth-child(even) { background: #eff6ff; }
    tbody tr:nth-child(odd)  { background: #ffffff; }
    tbody td { padding: 6px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }

    .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; }
    .badge-green  { background: #d1fae5; color: #065f46; }
    .badge-yellow { background: #fef3c7; color: #92400e; }
    .badge-red    { background: #fee2e2; color: #991b1b; }
    .badge-gray   { background: #f3f4f6; color: #374151; }

    .score { font-weight: bold; font-size: 12px; text-align: center; }
    .score-green  { color: #059669; }
    .score-yellow { color: #d97706; }
    .score-red    { color: #dc2626; }
    .score-gray   { color: #6b7280; }

    .footer { text-align: center; font-size: 8px; color: #9ca3af; margin-top: 10px; padding-top: 8px; border-top: 1px solid #e5e7eb; }
    .page-break { page-break-after: always; }

    .section-title { font-size: 11px; font-weight: bold; color: #1d4ed8; padding: 6px 20px;
        border-left: 4px solid #1d4ed8; margin: 0 0 8px; background: #eff6ff; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>📊 Rekap Nilai Siswa</h1>
    <p>{{ $course ? $course->title : 'Semua Mata Pelajaran' }}</p>
    <div class="meta">Dicetak: {{ now()->format('d M Y, H:i') }} WIB &nbsp;|&nbsp; Total data: {{ $answers->count() }} jawaban</div>
</div>

{{-- Statistik ringkasan --}}
@php
    $graded    = $answers->where('status', 'graded');
    $pending   = $answers->where('status', 'pending');
    $avgScore  = $graded->count() ? round($graded->avg('score'), 1) : null;
    $highest   = $graded->max('score');
    $lowest    = $graded->min('score');
    $pass      = $graded->where('score', '>=', 75)->count();
@endphp
<table style="margin: 0 0 16px; font-size: 9px;">
    <tr>
        <td style="width:20%; padding:8px; text-align:center; background:#eff6ff; border:1px solid #bfdbfe; border-radius:4px;">
            <div style="font-size:18px; font-weight:bold; color:#1d4ed8;">{{ $answers->count() }}</div>
            <div style="color:#6b7280; font-size:8px;">Total Jawaban</div>
        </td>
        <td style="width:4%;"></td>
        <td style="width:20%; padding:8px; text-align:center; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:4px;">
            <div style="font-size:18px; font-weight:bold; color:#059669;">{{ $graded->count() }}</div>
            <div style="color:#6b7280; font-size:8px;">Sudah Dinilai</div>
        </td>
        <td style="width:4%;"></td>
        <td style="width:20%; padding:8px; text-align:center; background:#fefce8; border:1px solid #fde68a; border-radius:4px;">
            <div style="font-size:18px; font-weight:bold; color:#d97706;">{{ $pending->count() }}</div>
            <div style="color:#6b7280; font-size:8px;">Belum Dinilai</div>
        </td>
        <td style="width:4%;"></td>
        <td style="width:20%; padding:8px; text-align:center; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:4px;">
            <div style="font-size:18px; font-weight:bold; color:#059669;">{{ $avgScore ?? '—' }}</div>
            <div style="color:#6b7280; font-size:8px;">Rata-rata</div>
        </td>
        <td style="width:4%;"></td>
        <td style="width:20%; padding:8px; text-align:center; background:#eff6ff; border:1px solid #bfdbfe; border-radius:4px;">
            <div style="font-size:18px; font-weight:bold; color:#1d4ed8;">{{ $pass }}</div>
            <div style="color:#6b7280; font-size:8px;">Nilai ≥ 75</div>
        </td>
    </tr>
</table>

{{-- Tabel Nilai --}}
<div class="section-title">Daftar Nilai</div>
<table>
    <thead>
        <tr>
            <th style="width:3%;">No</th>
            <th style="width:16%;">Nama Siswa</th>
            <th style="width:8%;">Kelas</th>
            <th style="width:18%;">Materi</th>
            <th style="width:16%;">Asesmen</th>
            <th style="width:7%; text-align:center;">Nilai</th>
            <th style="width:10%;">Status</th>
            <th style="width:12%;">Durasi</th>
            <th style="width:10%;">Dinilai</th>
        </tr>
    </thead>
    <tbody>
        @forelse($answers as $i => $answer)
        @php
            $score = $answer->score;
            $scoreClass = !$score ? 'score-gray' : ($score >= 75 ? 'score-green' : ($score >= 60 ? 'score-yellow' : 'score-red'));
            $badgeClass = $answer->status === 'graded' ? 'badge-green' : 'badge-gray';
            $badgeText  = $answer->status === 'graded' ? 'Dinilai' : 'Pending';
        @endphp
        <tr>
            <td style="text-align:center;">{{ $i + 1 }}</td>
            <td><strong>{{ $answer->studentSession->name ?? '—' }}</strong></td>
            <td>{{ $answer->studentSession->class ?? '—' }}</td>
            <td>{{ $answer->assessment->material->title ?? '—' }}</td>
            <td>{{ $answer->assessment->title ?? '—' }}</td>
            <td class="score {{ $scoreClass }}">{{ $score ?? '—' }}</td>
            <td><span class="badge {{ $badgeClass }}">{{ $badgeText }}</span></td>
            <td style="text-align:center;">
                @if($answer->duration_minutes)
                    {{ $answer->duration_minutes }} mnt
                @else
                    —
                @endif
            </td>
            <td style="font-size:8px;">{{ $answer->graded_at?->format('d/m/Y H:i') ?? '—' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align:center; padding:20px; color:#9ca3af;">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Rekap per materi --}}
@if($course && $rekapMateri->count())
<div class="section-title" style="margin-top:16px;">Rekap Per Materi</div>
<table>
    <thead>
        <tr>
            <th>Materi</th>
            <th>Asesmen</th>
            <th style="text-align:center;">Siswa</th>
            <th style="text-align:center;">Dinilai</th>
            <th style="text-align:center;">Tertinggi</th>
            <th style="text-align:center;">Terendah</th>
            <th style="text-align:center;">Rata-rata</th>
            <th style="text-align:center;">Lulus (≥75)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapMateri as $rekap)
        <tr>
            <td>{{ $rekap['materi'] }}</td>
            <td>{{ $rekap['asesmen'] }}</td>
            <td style="text-align:center;">{{ $rekap['total'] }}</td>
            <td style="text-align:center;">{{ $rekap['dinilai'] }}</td>
            <td style="text-align:center; font-weight:bold; color:#059669;">{{ $rekap['tertinggi'] }}</td>
            <td style="text-align:center; font-weight:bold; color:#dc2626;">{{ $rekap['terendah'] }}</td>
            <td style="text-align:center; font-weight:bold;">{{ $rekap['rata_rata'] }}</td>
            <td style="text-align:center;">{{ $rekap['lulus'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="footer">
    STKALL — Sistem Pembelajaran &nbsp;|&nbsp; {{ now()->format('d M Y H:i') }}
    @if($course) &nbsp;|&nbsp; {{ $course->title }} @endif
</div>

</body>
</html>
