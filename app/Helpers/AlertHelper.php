<?php

if (!function_exists('sweetAlert')) {
    function sweetAlert()
    {
        $script = "";

        // Alert success
        if (session('success')) {
            $script .= "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '" . session('success') . "',
                    timer: 3000,
                    showConfirmButton: false
                });
            </script>";
        }

        // Alert error
        if (session('error')) {
            $script .= "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '" . session('error') . "',
                    timer: 3000,
                    showConfirmButton: false
                });
            </script>";
        }

        // Konfirmasi hapus & logout
        $script .= "<script>
            document.addEventListener('DOMContentLoaded', function () {
                // Hapus
                document.querySelectorAll('form[data-confirm=\"true\"]').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Yakin ingin menghapus data ini?',
                            text: 'Data yang dihapus tidak dapat dikembalikan!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });

                // Logout
                const logoutLink = document.querySelector('[data-confirm=\"logout\"]');
                const logoutForm = document.getElementById('logout-form');
                if (logoutLink && logoutForm) {
                    logoutLink.addEventListener('click', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Yakin ingin logout?',
                            text: 'Anda akan keluar dari akun ini.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Logout',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                logoutForm.submit();
                            }
                        });
                    });
                }
            });
        </script>";

        return $script;
    }
}
