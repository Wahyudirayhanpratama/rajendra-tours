<!doctype html>
<html lang="en">

<head>

    @include('layouts._partials.headerspwa')
</head>

<body>

    <!-- App Header -->
    <div class="loginbg bg-po">
        <div class="section">
        </div>
    </div>
    <!-- * App Header -->

    <div id="appCapsule" class="full-height" style="margin-top:-400px;">
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
