@extends('layouts.app')

@section('title', 'Beranda - CekDulu')

@section('content')
<div class="row justify-content-center text-center mt-5">
    <div class="col-md-8">
        <h1 class="fw-bold text-primary">Selamat Datang di CekDulu</h1>
        <p class="lead">Platform Pengecekan, Pelaporan, dan Verifikasi Indikasi Penipuan Digital.</p>
        
        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Cek Rekening / Nomor Telepon</h5>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Masukkan Nomor/Link/Rekening mencurigakan..." aria-label="Search">
                    <button class="btn btn-primary" type="button">Cari</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection