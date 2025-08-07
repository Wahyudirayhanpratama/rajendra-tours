@extends('layouts.master')

@section('title', 'Data Customer')

@section('content')
    @php
        $admin = Auth::guard('admin')->user();
    @endphp
    @if ($admin)
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h1 class="m-0 font-weight-bold">Data Customer</h1>
                        <a href="{{ route('tambah-data-pelanggan') }}" class="btn btn-tambah text-white">Tambah Akun</a>
                    </div>
                </div>
            </section>

            <div class="container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama</th>
                            <th>No Hp</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pelanggan as $index => $user)
                            <tr>
                                <td class="text-center">{{ $pelanggan->firstItem() + $index }}</td>
                                <td>{{ $user->nama }}</td>
                                <td>{{ $user->no_hp }}</td>
                                <td>{{ $user->alamat }}</td>
                                <td class="text-center">
                                    <a href="{{ route('edit-data-pelanggan', $user->user_id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('hapus-data-pelanggan', $user->user_id) }}" method="POST"
                                        style="display:inline;" data-confirm="true">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data pelanggan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                    {{ $pelanggan->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="container mt-5">
            <div class="alert alert-danger text-center">
                Anda tidak memiliki akses ke halaman ini.
            </div>
        </div>
    @endif
@endsection

@push('headers')
    <style>
        .container {
            max-width: 98%;
            overflow-x: auto;
            margin-left: 10px;
        }
    </style>
@endpush
