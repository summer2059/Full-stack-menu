document.addEventListener("DOMContentLoaded", () => {
  // Dark/Light Mode toggle (optional)
  const toggleBtn = document.querySelector(".toggle-mode");
  toggleBtn?.addEventListener("click", () => {
    document.body.classList.toggle("dark");
    toggleBtn.textContent = document.body.classList.contains("dark")
      ? "☀️ Light Mode"
      : "🌙 Dark Mode";
  });

  // Cart toggle
  const cartBtn = document.querySelector(".toggle-cart");
  const cartBox = document.getElementById("cart");
  cartBtn?.addEventListener("click", () => cartBox.classList.toggle("active"));

  // Cart data
  let cart = [];
  const cartItems = document.getElementById("cart-items");
  const cartTotal = document.getElementById("cart-total");

  // Add item to cart with id, name, price
  window.addToCart = function(id, name, price) {
    const item = cart.find(i => i.id === id);
    if (item) item.qty++;
    else cart.push({ id, name, price, qty: 1 });
    renderCart();
  }

  // Render cart UI
  function renderCart() {
    cartItems.innerHTML = "";
    let total = 0;
    cart.forEach(item => {
      total += item.price * item.qty;
      cartItems.innerHTML += `
        <div class="cart-item">
          <span>${item.name} x${item.qty}</span>
          <span>$${(item.price * item.qty).toFixed(2)}</span>
        </div>`;
    });
    cartTotal.textContent = total.toFixed(2);
  }

  // Checkout form elements
  const checkoutForm = document.getElementById("checkout-form");
  const checkoutCartItems = document.getElementById("checkout-cart-items");

  // Open checkout: generate hidden inputs and show form
  document.querySelector(".checkout")?.addEventListener("click", () => {
    if (cart.length === 0) return alert("Cart is empty!");

    // Clear previous inputs
    checkoutCartItems.innerHTML = '';

    // Add hidden inputs for menu_ids[] and quantities[]
    cart.forEach(item => {
      checkoutCartItems.innerHTML += `
        <input type="hidden" name="menu_ids[]" value="${item.id}">
        <input type="hidden" name="quantities[]" value="${item.qty}">
      `;
    });

    checkoutForm.classList.add("active");
  });

  // Close checkout form
  function closeCheckout() {
    checkoutForm.classList.remove("active");
  }
  window.closeCheckout = closeCheckout;

  // Close cart (optional)
  function closeCart() {
    cartBox.classList.remove("active");
  }
  window.closeCart = closeCart;

  // On form submit (optional: handle frontend validation or ajax)
  // Here, just let the form submit naturally.
  // You can add custom JS here if needed.

  // Initial cart render
  renderCart();
});
