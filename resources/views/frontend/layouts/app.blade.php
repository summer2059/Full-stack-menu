<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Restaurant')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
