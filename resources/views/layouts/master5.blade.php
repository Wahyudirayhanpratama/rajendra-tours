<!doctype html>
<html lang="en">

<head>

    @include('layouts._partials.headerspwa')
</head>

<body class="bg-blue">
    <main>
        @yield('content')
    </main>

    @include('layouts._partials.bottom-nav')
    @include('layouts._partials.scriptspwa')
</body>

</html>
