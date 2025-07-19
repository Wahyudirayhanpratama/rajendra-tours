@extends('layouts.master9')

@section('title', 'Profil Developer')

@section('content')
    <div class="container my-5">
        <div class="card border-0 shadow-lg rounded-4 p-4 bg-light">
            <div class="row align-items-center g-4">
                <div class="col-md-4 text-center">
                    <img src="{{ asset('storage/foto_formal_wahyu.jpg') }}" alt="Foto Developer"
                        class="img-fluid border border-3 shadow-sm" style="width: 300px; height: 350px; object-fit: cover;">
                    <h5 class="mt-3 text-muted">Fullstack Web Developer</h5>
                </div>

                <div class="col-md-8">
                    <h2 class="fw-bold mb-3 text-primary">Wahyudi Rayhan Pratama</h2>
                    <p class="fs-6 mb-3 text-dark">
                        Seorang pengembang sistem Rajendra Tours yang berfokus pada pengembangan backend Laravel,
                        integrasi sistem pembayaran Midtrans, serta manajemen data pengguna dan pemesanan.
                    </p>

                    <ul class="list-unstyled fs-6">
                        <li class="mb-2">
                            <i class="fas fa-envelope text-danger me-2"></i>
                            <strong>Email:</strong>
                            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=wahyudirayhan11@gmail.com" target="_blank">
                                wahyudirayhan11@gmail.com
                            </a>
                        </li>
                        <li class="mb-2">
                            <i class="fab fa-github text-dark me-2"></i>
                            <strong>GitHub:</strong> <a href="https://github.com/Wahyudirayhanpratama/rajendra-tours.git"
                                target="_blank">github.com/Wahyudirayhanpratama</a>
                        </li>
                        <li class="mb-2">
                            <i class="fab fa-linkedin text-primary me-2"></i>
                            <strong>LinkedIn:</strong> <a href="https://linkedin.com/in/wahyudirayhanpratama"
                                target="_blank">linkedin.com/in/wahyudirayhanpratama</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt text-success me-2"></i>
                            <strong>Lokasi:</strong> Pekanbaru, Riau, Indonesia
                        </li>
                    </ul>

                    <div class="mt-4">
                        <a href="mailto:wahyudirayhan11@gmail.com" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-paper-plane me-1"></i> Hubungi Saya
                        </a>
                        <a href="https://github.com/Wahyudirayhanpratama/rajendra-tours.git" class="btn btn-dark btn-sm"
                            target="_blank">
                            <i class="fab fa-github me-1"></i> GitHub
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
