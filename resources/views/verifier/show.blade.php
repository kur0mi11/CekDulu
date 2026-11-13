@extends('layouts.app')

@section('title', 'Detail Pemeriksaan Laporan')

@section('content')
<div class="container mt-2 mb-5">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>Detail Pemeriksaan
            </h3>
            <p class="text-muted mt-1 mb-0">Tinjau informasi laporan dan tentukan keputusan verifikasi.</p>
        </div>
        <a href="{{ route('verifier.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Antrean
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            
            <!-- Card Informasi Laporan -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px;">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-0">Informasi Pengaduan</h5>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width: 150px;">Pelapor</td>
                                <td class="fw-medium text-dark">: {{ $report->user->name ?? 'Anonim' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Masuk</td>
                                <td class="fw-medium text-dark">: {{ $report->created_at->format('d M Y, H:i') }} WIB</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Target Laporan</td>
                                <td class="fw-bold text-danger fs-5">: {{ $report->reported_value }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kategori</td>
                                <td>: 
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fw-normal">
                                        {{ $report->category->name ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <hr class="text-muted opacity-25 my-4">

                    <h6 class="fw-bold text-dark mb-3">Kronologi Kejadian:</h6>
                    <div class="bg-light p-4 rounded-4 text-dark" style="border: 1px solid #f0f0f0;">
                        {{ $report->chronology ?? 'Tidak ada kronologi yang ditulis.' }}
                    </div>
                    
                    @if($report->status === 'pending' && $report->revision_note)
                        <div class="alert alert-warning mt-4 mb-0">
                            <strong>Catatan revisi sebelumnya:</strong>
                            <p class="mb-0">{{ $report->revision_note }}</p>
                            <p class="small text-muted mb-0 mt-2">Laporan ini sudah diperbaiki pelapor dan dikirim ulang untuk diperiksa.</p>
                        </div>
                    @endif
                    
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px;">
                        <i class="bi bi-clipboard2-check-fill fs-5"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-0">Keputusan Verifikasi</h5>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <!-- Pastikan route ini mengarah ke fungsi update controller Anda -->
                    <form action="{{ route('verifier.reports.update', $report->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Ubah Status Laporan <span class="text-danger">*</span></label>
                            <select name="status" class="form-select rounded-3 py-2" required id="statusSelect">
                                <option value="" disabled selected>-- Pilih Keputusan --</option>
                                <option value="pending_admin_review">✅ Teruskan ke Admin (Bukti Valid)</option>
                                <option value="need_revision">⚠️ Kembalikan ke Pelapor (Perlu Revisi)</option>
                                <option value="rejected">❌ Tolak Laporan (Tidak Valid)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Catatan Revisi <span class="text-muted small fw-normal">(Wajib diisi jika minta revisi)</span></label>
                            <textarea name="revision_note" id="revisionNote" class="form-control rounded-4" rows="4" placeholder="Jelaskan bagian mana yang perlu diperbaiki oleh pelapor..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 w-100 fw-bold shadow-sm">
                            Simpan Keputusan Verifikasi <i class="bi bi-check-circle ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex align-items-center">
                    <div class="bg-dark bg-opacity-10 text-dark rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px;">
                        <i class="bi bi-image fs-5"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-0">Bukti Lampiran</h5>
                </div>
                <div class="card-body px-4 pb-4 pt-2 text-center d-flex flex-column">
                    
                    <p class="text-muted small text-start mb-3">Dokumen/Foto bukti transaksi yang diunggah bersifat privat.</p>
                    
                    <div class="bg-light rounded-4 d-flex align-items-center justify-content-center mb-4 flex-grow-1 overflow-hidden" style="min-height: 250px; border: 2px dashed #dee2e6; position: relative;">
                        
                        @if($report->evidence_file)
                            @php
                                $extension = pathinfo($report->evidence_file, PATHINFO_EXTENSION);
                            @endphp

                            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                                <!-- Gambar mengambil src dari rute privat Anda -->
                                <img src="{{ route('verifier.reports.evidence', $report->id) }}" alt="Bukti Laporan" class="img-fluid" 
                                     onerror="this.outerHTML='<div class=\'text-center p-3\'><i class=\'bi bi-file-earmark-lock text-secondary opacity-50\' style=\'font-size: 4rem;\'></i><p class=\'text-muted small mt-3 fw-medium\'>Gambar tidak dapat dimuat.<br>Route Stream File belum berfungsi.</p></div>'">
                            @else
                                <div class="text-center p-3">
                                    <i class="bi bi-file-earmark-pdf text-danger opacity-75" style="font-size: 4rem;"></i>
                                    <p class="text-muted small mt-3 fw-medium">Format File: PDF</p>
                                </div>
                            @endif
                        @else
                            <div class="text-center p-3">
                                <i class="bi bi-file-earmark-x text-secondary opacity-50" style="font-size: 4rem;"></i>
                                <p class="text-muted small mt-3 fw-medium">Tidak ada file bukti yang dilampirkan.</p>
                            </div>
                        @endif
                    </div>

                    @if($report->evidence_file)
                        <a href="{{ route('verifier.reports.evidence', $report->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill py-2 w-100 fw-medium shadow-sm">
                            <i class="bi bi-arrows-fullscreen me-1"></i> Buka Ukuran Penuh
                        </a>
                    @else
                        <button class="btn btn-outline-secondary rounded-pill py-2 w-100 fw-medium" disabled>
                            <i class="bi bi-slash-circle me-1"></i> Tidak Ada File
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const statusSelect = document.getElementById('statusSelect');
        const revisionNote = document.getElementById('revisionNote');

        statusSelect.addEventListener('change', function() {
            if (this.value === 'need_revision') {
                revisionNote.setAttribute('required', 'required');
                revisionNote.classList.add('border-danger');
            } else {
                revisionNote.removeAttribute('required');
                revisionNote.classList.remove('border-danger');
            }
        });
    });
</script>
@endsection