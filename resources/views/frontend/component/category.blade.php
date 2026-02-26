<div class="category-bar" id="category-bar">
    <button class="category-btn active" data-category="all">All</button>
    @php
        $categories = $menuItems->pluck('menuCategory.title')->unique()->filter();
    @endphp
    @foreach($categories as $category)
        <button class="category-btn" data-category="{{ strtolower($category) }}">
            {{ ucfirst($category) }}
        </button>
    @endforeach
</div>