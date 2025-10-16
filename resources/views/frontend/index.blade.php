@extends('frontend.layouts.app')
@section('title', 'Restaurant Menu')
@section('content')

<!-- Header -->


<!-- Category Bar -->
<div class="category-bar d-flex flex-wrap justify-content-start px-3 py-2" id="category-bar">
    <button class="category-btn active me-2 mb-2" data-category="all">All</button>
    @php
        $categories = $menuItems->pluck('menuCategory.title')->unique();
    @endphp
    @foreach($categories as $category)
        <button class="category-btn me-2 mb-2" data-category="{{ strtolower($category) }}">
            {{ ucfirst($category) }}
        </button>
    @endforeach
</div>

<!-- Menu Section -->
<section class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 p-3" id="menu">
    @foreach($menuItems as $item)
        <div class="col menu-card" data-category="{{ strtolower($item->menuCategory->title ?? 'uncategorized') }}">
            <div class="card h-100 text-center">
                <div class="img-wrapper p-2">
                    <img src="{{ asset('uploads/images/' . $item->image) }}" alt="{{ $item->title }}" class="menu-image rounded-circle mx-auto d-block">
                </div>
                <div class="card-body">
                    <h5 class="menu-title">{{ $item->title }}</h5>
                    <p class="description">{{ Str::limit($item->description, 80) }}</p>
                    <div class="rating mb-2">
                        @for ($i = 0; $i < 5; $i++)
                            <i class="{{ $i < $item->rating ? 'fas' : 'far' }} fa-star"></i>
                        @endfor
                    </div>
                    <div class="price-section mb-3">
                        @if ($item->original_price && $item->original_price > $item->price)
                            <span class="original-price">NRs.{{ number_format($item->original_price, 0) }}</span>
                        @endif
                        <span class="discounted-price">NRs.{{ number_format($item->price, 0) }}</span>
                    </div>
                    <button class="cart-btn btn btn-primary w-100" onclick="addToCart({{ $item->id }}, '{{ $item->title }}', {{ $item->price }})">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</section>

@include('frontend.component.cart')
@include('frontend.component.checkout')
@endsection
