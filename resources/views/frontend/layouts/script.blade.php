<script src="{{ asset('frontend/js/script.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
{{-- <script>
    // Category Filter
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const category = btn.dataset.category;
            document.querySelectorAll('.menu .card').forEach(card => {
                card.style.display = (category === 'all' || card.dataset.category === category) ? 'block' : 'none';
            });
        });
    });
</script> --}}
