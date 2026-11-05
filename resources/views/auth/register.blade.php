@extends('layouts.app')

@section('title', 'Daftar Akun - CekDulu')

@section('content')
<div class="container d-flex justify-content-center align-items-center mb-5" style="min-height: 75vh;">
    <div class="card border-0 shadow-sm overflow-hidden" style="max-width: 850px; width: 100%; border-radius: 16px;">
        <div class="row g-0">
            
            <div class="col-md-5 d-none d-md-block bg-primary">
                <div class="d-flex flex-column justify-content-center align-items-center h-100 text-white p-5 text-center position-relative overflow-hidden">
                    <i class="bi bi-shield-check opacity-25 position-absolute" style="font-size: 15rem; right: -30px; bottom: -50px;"></i>
                    <div class="z-index-1">
                        <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-4 shadow-sm" style="width: 70px; height: 70px;">
                            <i class="bi bi-person-plus fs-2"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Gabung CekDulu</h4>
                        <p class="text-white opacity-75 small">Bantu ciptakan ekosistem digital yang aman dengan melaporkan indikasi penipuan di sekitar Anda.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card-body p-4 p-md-5">
                    <h5 class="fw-bold text-dark mb-1">Daftar Akun Baru</h5>
                    <p class="text-muted small mb-4">Lengkapi data di bawah ini untuk membuat akun.</p>

                    <form action="{{ route('register') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark small mb-1">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control rounded-3 py-2 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukkan nama Anda" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark small mb-1">Email</label>
                            <input type="email" name="email" class="form-control rounded-3 py-2 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="contoh@email.com" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark small mb-1">Kata Sandi</label>
                            <input type="password" name="password" class="form-control rounded-3 py-2 @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium text-dark small mb-1">Konfirmasi Kata Sandi</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3 py-2" placeholder="Ulangi kata sandi" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm mb-3">
                            Daftar Sekarang
                        </button>

                        <div class="text-center mt-2">
                            <p class="text-muted small mb-0">Sudah punya akun? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Masuk di sini</a></p>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection