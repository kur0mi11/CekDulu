@extends('layouts.app')

@section('title', 'Dashboard - CekDulu')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mt-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0">Halo, {{ Auth::user()->name }}</h3>
        <p class="text-muted mb-0">Anda masuk sebagai {{ $role }}.</p>
    </div>
    @if($role === 'Admin')
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary">Kelola Kategori</a>
    @endif
</div>

@if($role === 'User')
    @include('dashboard.user')
@elseif($role === 'Verifier')
    @include('dashboard.verifier')
@elseif($role === 'Admin')
    @include('dashboard.admin')
@endif
@endsection