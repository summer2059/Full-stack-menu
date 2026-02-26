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
{{-- @include('frontend.component.checkout') --}}
@include('frontend.component.checkout', ['tableNumber' => $tableNumber ?? null])

@endsection

@section('scripts')
<script src="{{ asset('frontend/js/script.js') }}"></script>
@endsection