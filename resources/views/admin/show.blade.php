@extends('layouts.app')

@section('title', 'Tinjau Laporan - CekDulu')

@section('content')
<div class="mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Tinjau Laporan</h3>
            <p class="text-muted small mb-0">Periksa data dan bukti, lalu tetapkan keputusan akhir.</p>
        </div>
        <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">Kembali ke Antrean</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        <div class="col-lg-8">

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Laporan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width: 170px;">Nomor Telepon</td>
                                <td class="fw-bold fs-5">{{ $report->reported_value }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kategori</td>
                                <td>{{ $report->category->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td>@include('partials.status-badge', ['report' => $report])</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pelapor</td>
                                <td>{{ $report->user->name ?? '-' }} ({{ $report->user->email ?? '-' }})</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Dikirim</td>
                                <td>{{ $report->created_at->format('d M Y, H:i') }} WIB</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Diperiksa Verifier</td>
                                <td>{{ $report->verifier->name ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <hr>
                    <p class="fw-bold mb-2">Kronologi dari Pelapor</p>
                    <div class="p-3 bg-light rounded border">{{ $report->chronology }}</div>
                </div>
            </div>

            @if($report->status === 'pending_admin_review')

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Setujui dan Publikasikan</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.reports.approve', $report->id) }}" method="POST"
                              onsubmit="return confirm('Setujui laporan ini? Ringkasan akan tampil di pencarian publik.')">
                            @csrf
                            @method('PATCH')

                            <label for="public_summary" class="form-label fw-bold">Ringkasan untuk Publik <span class="text-danger">*</span></label>
                            <textarea name="public_summary" id="public_summary" rows="4" maxlength="500"
                                      class="form-control @error('public_summary') is-invalid @enderror"
                                      placeholder="Contoh: Pelapor dihubungi dengan modus tawaran hadiah dan diminta mentransfer biaya administrasi.">{{ old('public_summary') }}</textarea>
                            <div class="form-text">
                                Tulis ulang secara singkat. Jangan memuat nama, nomor rekening, alamat, atau data pribadi lain. Maksimal 500 karakter.
                            </div>

                            <button type="submit" class="btn btn-success mt-3">Setujui dan Publikasikan</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Tolak Laporan</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.reports.reject', $report->id) }}" method="POST"
                              onsubmit="return confirm('Tolak laporan ini? Laporan tidak akan dipublikasikan.')">
                            @csrf
                            @method('PATCH')

                            <label for="rejection_reason" class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="3" maxlength="1000"
                                      class="form-control @error('rejection_reason') is-invalid @enderror"
                                      placeholder="Jelaskan alasan laporan tidak dapat dipublikasikan.">{{ old('rejection_reason') }}</textarea>
                            <div class="form-text">Alasan ini dapat dilihat oleh pelapor.</div>

                            <button type="submit" class="btn btn-outline-danger mt-3">Tolak Laporan</button>
                        </form>
                    </div>
                </div>

            @else

                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">Keputusan Akhir</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Diputuskan oleh:</strong> {{ $report->admin->name ?? '-' }}</p>
                        <p class="mb-3"><strong>Tanggal:</strong> {{ $report->updated_at->format('d M Y, H:i') }} WIB</p>

                        @if($report->status === 'verified')
                            <p class="fw-bold mb-1">Ringkasan yang tampil di publik</p>
                            <div class="p-3 bg-light rounded border">{{ $report->public_summary ?? '-' }}</div>
                        @else
                            <p class="fw-bold mb-1">Alasan Penolakan</p>
                            <div class="p-3 bg-light rounded border">{{ $report->revision_note ?? '-' }}</div>
                        @endif
                    </div>
                </div>

            @endif
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white d-flex align-items-center gap-2">
                    @include('partials.icon', ['name' => 'lock', 'size' => 16])
                    <h5 class="mb-0">Bukti Lampiran</h5>
                </div>
                <div class="card-body text-center">
                    @if($report->evidence_file)
                        @php $ext = strtolower(pathinfo($report->evidence_file, PATHINFO_EXTENSION)); @endphp

                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                            <img src="{{ route('admin.reports.evidence', $report->id) }}" alt="Bukti laporan" class="img-fluid rounded border mb-3">
                        @else
                            <p class="text-muted small">Bukti berupa file PDF.</p>
                        @endif

                        <a href="{{ route('admin.reports.evidence', $report->id) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">Buka File Bukti</a>
                        <p class="text-muted small mt-2 mb-0">File bersifat privat dan hanya dapat dibuka oleh petugas berwenang.</p>
                    @else
                        <p class="text-muted mb-0">Tidak ada file bukti.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection