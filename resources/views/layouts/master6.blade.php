<!doctype html>
<html lang="en">

<head>

    @include('layouts._partials.headerspwa')
</head>

<body class="bg-blue" style="height: 100vh;">
    <main>
        @yield('content')
    </main>
    @include('layouts._partials.scriptspwa')
</body>

</html>
