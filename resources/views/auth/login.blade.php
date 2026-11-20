@extends('layouts.app')

@section('title', 'Masuk - CekDulu')

@section('content')
<div class="container d-flex justify-content-center align-items-center mb-5" style="min-height: 75vh;">
    <div class="card border-0 shadow-sm overflow-hidden" style="max-width: 850px; width: 100%; border-radius: 16px;">
        <div class="row g-0">
            
            <div class="col-md-5 d-none d-md-block bg-primary">
                <div class="d-flex flex-column justify-content-center align-items-center h-100 text-white p-5 text-center position-relative overflow-hidden">

                    <i class="bi bi-shield-lock opacity-25 position-absolute" style="font-size: 15rem; right: -30px; bottom: -50px;"></i>
                    
                    <div class="z-index-1">
                        <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-4 shadow-sm" style="width: 70px; height: 70px;">
                            <i class="bi bi-box-arrow-in-right fs-2"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Selamat Datang</h4>
                        <p class="text-white opacity-75 small">Masuk untuk melanjutkan proses verifikasi atau memantau status laporan indikasi penipuan Anda.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card-body p-4 p-md-5">
                    <h5 class="fw-bold text-dark mb-1">Masuk ke CekDulu</h5>
                    <p class="text-muted small mb-4">Silakan masukkan email dan kata sandi Anda.</p>

                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark small mb-1">Email</label>
                            <input type="email" name="email" class="form-control rounded-3 py-2 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Masukkan email..." required>
                            @error('email') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium text-dark small mb-1">Kata Sandi</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control rounded-start-3 py-2 @error('password') is-invalid @enderror" placeholder="Masukkan password..." required autocomplete="current-password">
                                <button class="btn btn-outline-secondary border-start-0 rounded-end-3 px-3 d-flex align-items-center password-toggle-btn" type="button" data-toggle-password="password" aria-label="Tampilkan kata sandi" title="Tampilkan kata sandi">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password') 
                                <div class="invalid-feedback d-block">{{ $message }}</div> 
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm mb-3">
                            Masuk 
                        </button>

                        <div class="text-center mt-2">
                            <p class="text-muted small mb-0">Belum punya akun? <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Daftar di sini</a></p>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection