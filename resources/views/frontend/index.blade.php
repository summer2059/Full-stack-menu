@extends('frontend.layouts.app')

@section('title', 'Restaurant Menu')

@section('content')

{{-- ── Hero Banner ── --}}
@include('frontend.component.hero')

{{-- ── Category Bar ── --}}
@include('frontend.component.category')

{{-- ── Menu Grid ── --}}
@include('frontend.component.menu')

{{-- Cart backdrop --}}
<div class="cart-backdrop" id="cart-backdrop"></div>

@include('frontend.component.cart')
@include('frontend.component.checkout', ['tableNumber' => $tableNumber ?? null])
@include('frontend.component.track-order')

{{-- Inject menu items for JS recommended section --}}
@php
    $menuData = $menuItems->map(function ($m) {
        return [
            'id'        => $m->id,
            'title'     => $m->title,
            'price'     => $m->price,
            'image_url' => filter_var($m->image, FILTER_VALIDATE_URL)
                ? $m->image
                : asset('uploads/images/' . $m->image),
        ];
    });
@endphp

<script>
    window.MENU_ITEMS = @json($menuData);
</script>

@endsection

@section('scripts')
<script src="{{ asset('frontend/js/script.js') }}"></script>
@endsection