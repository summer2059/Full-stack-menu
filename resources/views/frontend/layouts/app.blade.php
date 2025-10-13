<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Restaurant')</title>

    {{-- Styles --}}
    @include('frontend.layouts.style')
    @stack('styles')
</head>
<body>

    {{-- Header --}}
    @include('frontend.layouts.header')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Scripts --}}
    @include('frontend.layouts.script')
    @stack('scripts')
</body>
</html>
