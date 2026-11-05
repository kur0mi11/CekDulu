@extends('layouts.app')

@section('title', 'Beranda - CekDulu')

@section('content')
<div class="row justify-content-center text-center mt-5">
    <div class="col-md-8">
        <h1 class="fw-bold text-primary">Selamat Datang di CekDulu</h1>
        <p class="lead">Platform pencarian informasi nomor telepon yang terindikasi penipuan</p>
        
        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Cek Nomor Telepon</h5>
                <form class="d-flex" action="{{ url('/') }}" method="GET">
                    <input class="form-control me-2" type="search" name="nomor" value="{{ request('nomor') }}" placeholder="Masukkan nomor telepon..." aria-label="Search" required>
                    <button class="btn btn-primary" type="submit">Cari</button>
                </form>
            </div>
        </div>

        @if(isset($query))
            <div class="mt-4 text-start">
                @if($laporan)
                    <div class="alert alert-warning">
                        <h5 class="alert-heading">Nomor ini memiliki laporan terverifikasi terkait indikasi penipuan.</h5>
                        <hr>
                        <p class="mb-1"><strong>Nomor Telepon:</strong> {{ $laporan->reported_value }}</p>
                        <p class="mb-1"><strong>Kategori:</strong> {{ $laporan->category->name ?? 'Tidak diketahui' }}</p>
                        <p class="mb-2"><strong>Ringkasan:</strong> {{ $laporan->public_summary ?? Str::limit($laporan->chronology, 200) }}</p>
                        <p class="small text-muted mb-0">Status terverifikasi berarti laporan lolos pemeriksaan internal CekDulu. Ini bukan putusan hukum dan tidak membuktikan identitas pemilik nomor.</p>
                    </div>
                @else
                    <div class="alert alert-info">
                        Belum ditemukan laporan terverifikasi untuk nomor ini.
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection