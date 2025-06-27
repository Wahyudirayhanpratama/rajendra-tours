<!doctype html>
<html lang="en">

<head>

    @include('layouts._partials.headers')
</head>

<body>
    @include('layouts._partials.navbars')
    @include('layouts._partials.sidebar')

    <main>
        @yield('content')
    </main>

    @include('layouts._partials.scripts')
    @include('layouts._partials.footers')
</body>

</html>
