@extends('layouts.app')

@section('title', 'Beranda - CekDulu')

@section('content')
<div class="row justify-content-center text-center mt-5 mb-5">
    <div class="col-md-8">
        <h1 class="fw-bold text-primary">Selamat Datang di CekDulu</h1>
        <p class="lead">Platform pencarian informasi nomor telepon yang terindikasi penipuan</p>

        <div class="card mt-4 shadow-sm">
            <div class="card-body text-start">
                <h5 class="card-title mb-3">Cek Nomor Telepon</h5>

                <form class="d-flex" action="{{ url('/') }}" method="GET">
                    <input class="form-control me-2 {{ $error ? 'is-invalid' : '' }}" type="search" name="nomor"
                           value="{{ $query }}" placeholder="Contoh: 081234567890"
                           inputmode="tel" maxlength="30" aria-label="Nomor telepon"
                           data-phone-input="#phone-hint" required>
                    <button class="btn btn-primary" type="submit">Cari</button>
                </form>
                <div id="phone-hint" class="form-text">Bisa ditulis 0812..., +62812..., atau 62812...</div>
            </div>
        </div>

        @if($error)
            <div class="alert alert-danger mt-4 text-start">{{ $error }}</div>
        @elseif($query !== '')
            <div class="mt-4 text-start">
                @if($laporan->isNotEmpty())
                    <div class="alert alert-warning">
                        <h5 class="alert-heading">Nomor ini memiliki laporan terverifikasi terkait indikasi penipuan.</h5>
                        <p class="mb-0">
                            Nomor <strong>{{ $normalized }}</strong>, {{ $laporan->count() }} laporan terverifikasi.
                        </p>
                    </div>

                    @foreach($laporan as $item)
                        <div class="card shadow-sm mb-3">
                            <div class="card-body">
                                <p class="mb-1"><strong>Kategori:</strong> {{ $item['kategori'] }}</p>
                                <p class="mb-0"><strong>Ringkasan:</strong> {{ $item['ringkasan'] }}</p>
                            </div>
                        </div>
                    @endforeach

                    <p class="small text-muted">
                        Status terverifikasi berarti laporan lolos pemeriksaan internal CekDulu.
                        Ini bukan putusan hukum dan tidak membuktikan identitas pemilik nomor.
                    </p>
                @else
                    <div class="alert alert-info">
                        Belum ditemukan laporan terverifikasi untuk nomor ini.
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