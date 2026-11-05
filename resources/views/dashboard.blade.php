@extends('layouts.app')

@section('title', 'Dashboard - CekDulu')

@section('content')
<div class="mt-2 mb-4">
    <h3 class="fw-bold mb-0">Halo, {{ Auth::user()->name }}</h3>
    <p class="text-muted mb-0">Anda masuk sebagai {{ $role }}.</p>
</div>

@if($role === 'User')
    @include('dashboard.user')
@elseif($role === 'Verifier')
    @include('dashboard.verifier')
@elseif($role === 'Admin')
    @include('dashboard.admin')
@endif
@endsection