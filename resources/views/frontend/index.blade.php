@extends('frontend.layouts.app')

@section('title', 'Restaurant Menu')

@section('content')
    <!-- Category Bar -->
    <div class="category-bar" id="category-bar">
        <button class="category-btn active" data-category="all">All</button>

        @php
            $categories = $menuItems->pluck('menuCategory.title')->unique();
        @endphp

        @foreach($categories as $category)
            <button class="category-btn" data-category="{{ strtolower($category) }}">{{ ucfirst($category) }}</button>
        @endforeach
    </div>

    <!-- Menu Section -->
    <section class="menu" id="menu">
        @foreach($menuItems as $item)
            <div class="menu-card" data-category="{{ strtolower($item->menuCategory->title ?? 'uncategorized') }}">
                <div class="img-wrapper">
                    <img src="{{ asset('uploads/images/' . $item->image) }}" alt="{{ $item->title }}" class="menu-image">
                </div>

                <div class="card-body">
                    <h3 class="menu-title">{{ $item->title }}</h3>
                    <p class="description">{{ Str::limit($item->description, 80) }}</p>

                    <div class="rating">
                        @for ($i = 0; $i < 5; $i++)
                            @if ($i < $item->rating)
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>

                    <div class="price-section">
                        @if ($item->original_price && $item->original_price > $item->price)
                            <span class="original-price">NRs.{{ number_format($item->original_price, 0) }}</span>
                        @endif
                        <span class="discounted-price">NRs.{{ number_format($item->price, 0) }}</span>
                    </div>

                    <button class="cart-btn" onclick="addToCart('{{ $item->title }}', {{ $item->price }})">
                        <i class="fas fa-shopping-cart"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </section>

    {{-- Cart & Checkout --}}
    @include('frontend.component.cart')
    @include('frontend.component.checkout')
@endsection
