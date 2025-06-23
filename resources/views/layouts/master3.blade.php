<!doctype html>
<html lang="en">

<head>

    @include('layouts._partials.headerspwa')
</head>

<body>

    <!-- loader -->
    <div id="loader">
        <div class="spinner-border text-light" role="status"></div>
    </div>
    <!-- * loader -->

    <!-- App Header -->
    <div class="loginbg bg-po">
        <div class="section">
        </div>
    </div>
    <!-- * App Header -->

    <div id="appCapsule" style="margin-top:-380px;">
        @yield('content')

    </div>


    <!-- toast top auto close in 2 seconds -->
    <div id="toast-8" class="toast-box toast-bottom">
        <div class="in">
            <div class="text" id="toastmessage"></div>
        </div>
    </div>

    @include('layouts._partials.bottom-nav')

    @include('layouts._partials.scriptspwa')



</body>

</html>
