<!-- JQUERY -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ asset('js/boostrap.bundle.min.js') }}"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="{{ asset('js/splide.min.js') }}"></script>
<script src="{{ asset('js/base.js') }}"></script>
<link href="{{ asset('css/caleran.min.css') }}" rel="stylesheet" />
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/caleran.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- CDN SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Panggil helper --}}
{!! sweetAlert() !!}

{{-- PWA --}}
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('{{ asset('service-worker.js') }}')
            .then(function(reg) {
                console.log("✅ Service worker registered!", reg);
            })
            .catch(function(err) {
                console.log("❌ Service worker registration failed: ", err);
            });
    }
</script>

@stack('scriptspwa')
