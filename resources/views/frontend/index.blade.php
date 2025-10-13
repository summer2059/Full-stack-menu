@extends('frontend.layouts.app')

@section('title', 'Restaurant Menu')

@section('content')
    <!-- Category Bar -->
    <div class="category-bar" id="category-bar">
        <button class="category-btn active" data-category="all">All</button>

        @php
            $categories = collect($menuItems)->pluck('category')->unique();
        @endphp

        @foreach($categories as $category)
            <button class="category-btn" data-category="{{ $category }}">{{ ucfirst($category) }}</button>
        @endforeach
    </div>

    <!-- Menu Section -->
    <section class="menu" id="menu">
        @foreach($menuItems as $item)
            <div class="card" data-category="{{ $item->category }}">
                <img src="{{ $item->image }}" alt="{{ $item->name }}">
                <h3>{{ $item->name }}</h3>
                <p>${{ number_format($item->price, 2) }}</p>
                <button onclick="addToCart('{{ $item->name }}', {{ $item->price }})">Add to Cart</button>
            </div>
        @endforeach
    </section>

    {{-- Cart & Checkout --}}
    @include('frontend.component.cart')
    @include('frontend.component.checkout')
@endsection
