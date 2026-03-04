<section id="menu">
    @foreach($menuItems as $item)
        <div class="menu-card" data-category="{{ strtolower($item->menuCategory->title ?? 'uncategorized') }}">
            @php
                $image = $item->image;
                if (filter_var($image, FILTER_VALIDATE_URL)) {
                    $imageSrc = $image;
                } else {
                    $imageSrc = asset('uploads/images/' . $image);
                }
            @endphp

            {{-- Image --}}
            <div class="img-wrapper">
                <img src="{{ $imageSrc }}"
                     alt="{{ $item->title }}"
                     class="menu-image"
                     loading="lazy">
                @if ($item->original_price && $item->original_price > $item->price)
                    <span class="img-badge">SALE</span>
                @endif
            </div>

            {{-- Body --}}
            <div class="card-body">
                <h3 class="menu-title">{{ $item->title }}</h3>
                <p class="description">{{ Str::limit($item->description, 80) }}</p>

                {{-- Stars --}}
                <div class="rating">
                    @for ($i = 0; $i < 5; $i++)
                        <i class="{{ $i < ($item->rating ?? 0) ? 'fas' : 'far' }} fa-star"></i>
                    @endfor
                </div>

                {{-- Price --}}
                <div class="price-section">
                    @if ($item->original_price && $item->original_price > $item->price)
                        <span class="original-price">NRs.{{ number_format($item->original_price, 0) }}</span>
                    @endif
                    <span class="discounted-price">NRs.{{ number_format($item->price, 0) }}</span>
                </div>

                {{-- Add to Cart — text hidden on mobile, icon only --}}
                <button class="cart-btn"
                        data-item-id="{{ $item->id }}"
                        onclick="addToCart({{ $item->id }}, '{{ addslashes($item->title) }}', {{ $item->price }})">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="btn-text"> Add to Cart</span>
                </button>
            </div>
        </div>
    @endforeach
</section>