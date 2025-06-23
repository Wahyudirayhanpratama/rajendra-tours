@extends('layouts.master1')

@section('title', 'Login')

@section('content')

    <div class="login-wrapper">
        <div class="login-container">
            <!-- Ganti dengan path gambar logo Rajendra Tours Anda -->
            <img src="{{ asset('storage/logo_rajendra.png') }}" alt="Rajendra Tours" class="logo">
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-po text-white">Login</button>
            </form>
            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    {{ $errors->first() }}
                </div>
            @endif
        </div>
    </div>
@endsection
