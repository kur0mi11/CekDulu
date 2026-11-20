@extends('layouts.app')

@section('title', 'Beranda - CekDulu')

@section('content')
<div class="row justify-content-center text-center mt-5 mb-5">
    <div class="col-md-9 col-lg-8">
        <h1 class="fw-bold text-primary">Selamat Datang di CekDulu</h1>
        <p class="lead text-secondary">Platform pencarian informasi nomor telepon dan rekening bank yang terindikasi penipuan</p>

        <div class="card mt-4 shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                <ul class="nav nav-pills nav-fill" id="searchTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ ($type ?? 'phone') === 'phone' ? 'active' : '' }} fw-semibold rounded-pill py-2" 
                                id="phone-tab" data-bs-toggle="pill" data-bs-target="#phone-search-pane" type="button" role="tab">
                            <i class="bi bi-telephone me-1"></i> Cek Nomor Telepon
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ ($type ?? 'phone') === 'bank_account' ? 'active' : '' }} fw-semibold rounded-pill py-2" 
                                id="bank-tab" data-bs-toggle="pill" data-bs-target="#bank-search-pane" type="button" role="tab">
                            <i class="bi bi-credit-card-2-front me-1"></i> Cek Rekening / E-Wallet
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4 text-start">
                <div class="tab-content" id="searchTabContent">
                    <!-- Tab Cek Telepon -->
                    <div class="tab-pane fade {{ ($type ?? 'phone') === 'phone' ? 'show active' : '' }}" id="phone-search-pane" role="tabpanel">
                        <form class="d-flex" action="{{ url('/') }}" method="GET">
                            <input type="hidden" name="tipe" value="phone">
                            <input class="form-control me-2 {{ $error && ($type ?? 'phone') === 'phone' ? 'is-invalid' : '' }}" 
                                   type="search" name="nomor"
                                   value="{{ ($type ?? 'phone') === 'phone' ? $query : '' }}" 
                                   placeholder="Contoh: 081234567890"
                                   inputmode="tel" maxlength="30" aria-label="Nomor telepon"
                                   data-phone-input="#phone-hint" required>
                            <button class="btn btn-primary px-4 fw-semibold" type="submit">Cari</button>
                        </form>
                        <div id="phone-hint" class="form-text mt-2 text-muted">Bisa ditulis 0812..., +62812..., atau 62812...</div>
                    </div>

                    <!-- Tab Cek Rekening -->
                    <div class="tab-pane fade {{ ($type ?? 'phone') === 'bank_account' ? 'show active' : '' }}" id="bank-search-pane" role="tabpanel">
                        <form action="{{ url('/') }}" method="GET">
                            <input type="hidden" name="tipe" value="bank_account">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <select name="bank" class="form-select">
                                        <option value="">Semua Bank / E-Wallet</option>
                                        @foreach($banks as $bKey => $bLabel)
                                            <option value="{{ $bKey }}" {{ ($bank ?? '') === $bKey ? 'selected' : '' }}>
                                                {{ $bLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control {{ $error && ($type ?? 'phone') === 'bank_account' ? 'is-invalid' : '' }}" 
                                           type="search" name="nomor"
                                           value="{{ ($type ?? 'phone') === 'bank_account' ? $query : '' }}" 
                                           placeholder="Nomor rekening / akun"
                                           inputmode="numeric" maxlength="30" aria-label="Nomor rekening" required>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary w-100 fw-semibold" type="submit">Cari</button>
                                </div>
                            </div>
                        </form>
                        <div class="form-text mt-2 text-muted">Masukkan nomor rekening bank atau nomor akun dompet digital (minimal 5 karakter).</div>
                    </div>
                </div>
            </div>
        </div>

        @if($error)
            <div class="alert alert-danger mt-4 text-start shadow-sm">{{ $error }}</div>
        @elseif($query !== '')
            <div class="mt-4 text-start">
                @if($laporan->isNotEmpty())
                    <div class="alert alert-warning shadow-sm">
                        <h5 class="alert-heading fw-bold">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ ($type ?? 'phone') === 'bank_account' ? 'Rekening / Akun ini terindikasi penipuan!' : 'Nomor telepon ini memiliki laporan terverifikasi!' }}
                        </h5>
                        <p class="mb-0">
                            @if(($type ?? 'phone') === 'bank_account')
                                Rekening: <strong>{{ $targetInfo['bank_name'] ?? $bank ?? 'Bank' }} - {{ $normalized }}</strong>
                                @if(!empty($targetInfo['account_holder']))
                                    (a.n. <strong>{{ $targetInfo['account_holder'] }}</strong>)
                                @endif
                            @else
                                Nomor: <strong>{{ $normalized }}</strong>
                            @endif
                            , total <strong>{{ $laporan->count() }}</strong> laporan terverifikasi.
                        </p>
                    </div>

                    @foreach($laporan as $item)
                        <div class="card shadow-sm border-0 mb-3 rounded-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        {{ $item['kategori'] }}
                                    </span>
                                    @if(!empty($item['bank_name']))
                                        <span class="badge bg-light text-secondary border">
                                            {{ $item['bank_name'] }}
                                        </span>
                                    @endif
                                </div>
                                @if(!empty($item['account_holder']))
                                    <p class="mb-1 text-muted small"><strong>Atas Nama:</strong> {{ $item['account_holder'] }}</p>
                                @endif
                                <p class="mb-0 text-dark"><strong>Ringkasan Modus:</strong> {{ $item['ringkasan'] }}</p>
                            </div>
                        </div>
                    @endforeach

                    <p class="small text-muted mt-2">
                        <i class="bi bi-shield-check me-1"></i>
                        Status terverifikasi berarti laporan lolos pemeriksaan internal CekDulu.
                        Ini bukan putusan hukum dan tidak membuktikan identitas pemilik nomor/rekening.
                    </p>
                @else
                    <div class="alert alert-info shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Belum ditemukan laporan penipuan yang terverifikasi untuk {{ ($type ?? 'phone') === 'bank_account' ? 'rekening' : 'nomor' }} ini.
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

@push('scripts')
    @include('partials.phone-input-script')
@endpush
@endsection
