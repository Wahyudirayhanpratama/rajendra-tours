@extends('layouts.master10')

@section('title', 'Selamat Datang di Rajendra Tours')

@section('content')
    <!-- Section Landing -->
    <div class="text-center">
        <img src="{{ asset('storage/logo_rajendra.png') }}" alt="Logo Rajendra" class="img-fluid mb-4 mt-5"
            style="width: 250px;">

        <h2 class="fw-bold text-white mb-3" style="font-size: 20px;">PESAN TRAVEL RESMI HANYA DI RAJENDRATOURS</h2>
        <h2 class="fw-bold text-white mb-3" style="font-size: 20px;">DURI - PEKANBARU - PADANG</h2>

    </div>
    <div class="text-center">
        <a href="{{ route('cari-jadwal') }}" class="btn btn-po position-fixed start-50 translate-middle-x"
            style="bottom: 30px;">
            Cari Tiket Sekarang
        </a>
    </div>

@endsection

@push('headerspwa')
    <style>
        body {
            margin: 0;
            overflow: hidden;
            background: url('{{ asset('storage/unit_rajendra.jpg') }}') no-repeat center center;
            background-size: cover;
        }
    </style>
@endpush

@push('scriptspwa')
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(function(registration) {
                    console.log('ServiceWorker registered with scope:', registration.scope);
                })
                .catch(function(error) {
                    console.log('ServiceWorker registration failed:', error);
                });
        }
    </script>
@endpush
