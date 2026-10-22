@extends('layouts.app')

@section('title', 'Dashboard - CekDulu')

@section('content')
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Dashboard Pengguna</h5>
            </div>
            <div class="card-body">
                <h4>Selamat datang, {{ Auth::user()->name }}!</h4>
                
                @if(Auth::user()->role_id == 3)
                    <p class="text-danger fw-bold">Anda masuk sebagai Administrator.</p>
                    <p>Di sini Anda bisa meninjau laporan yang sudah diverifikasi, dan mengelola pengguna.</p>
                @elseif(Auth::user()->role_id == 2)
                    <p class="text-primary fw-bold">Anda masuk sebagai Verifier.</p>
                    <p>Silakan periksa laporan terbaru yang masuk dari masyarakat dan berikan catatan jika perlu direvisi.</p>
                @else
                    <p class="text-success fw-bold">Anda masuk sebagai Masyarakat (User).</p>
                    <p>Anda dapat memantau status laporan Anda pada menu "Daftar Laporan".</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection