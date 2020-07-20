
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PP Mall')</title>
    <!-- css -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    {{-- load strip js --}}
    @if (Str::contains(url()->current(), '/checkout/'))
        <script src="https://js.stripe.com/v3/"></script>
        @yield('stripeJs')
    @endif
</head>
<body>
    {{-- <h3>cuurent url: {{ url()->current()}}</h3>
    <h3>contains '/checkout/' {{Str::contains(url()->current(), '/checkout/')}}</h3> --}}
    <div id="app" class="{{ route_class() }}-page">
        @include('layouts._header')
        <div class="container">
            @yield('content')
        </div>
        @include('layouts._footer')
    </div>
    <!-- JS -->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD8TJCdNV16dWDi86u9dTU2s7rUVepqGpg&libraries=places"></script>
    <script src="{{ mix('js/app.js') }}"></script>
    @yield('scriptsAfterJs')
</body>
</html>