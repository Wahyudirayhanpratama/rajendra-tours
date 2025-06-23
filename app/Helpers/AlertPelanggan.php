<?php

namespace App\Helpers;

class AlertPelanggan
{
    public static function renderScript(): string
    {
        $script = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

        // Cek jika session memiliki flash message
        if (session()->has('success')) {
            $script .= '
            <script>
                Swal.fire({
                    icon: "success",
                    title: "Sukses!",
                    text: "' . session('success') . '",
                    background: "#f7f7f7",
                    color: "#333",
                    iconColor: "#198754",
                    confirmButtonColor: "#198754",
                    customClass: {
                        popup: "rounded-4 shadow-sm",
                        confirmButton: "btn btn-sm btn-success"
                    },
                    buttonsStyling: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            </script>';
        }

        if (session()->has('error')) {
            $script .= '
            <script>
                Swal.fire({
                    icon: "error",
                    title: "Oops!",
                    text: "' . session('error') . '",
                    background: "#f7f7f7",
                    color: "#333",
                    iconColor: "#dc3545",
                    confirmButtonColor: "#dc3545",
                    customClass: {
                        popup: "rounded-4 shadow-sm",
                        confirmButton: "btn btn-sm btn-danger"
                    },
                    buttonsStyling: false
                });
            </script>';
        }

        return $script;
    }
}
