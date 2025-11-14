document.addEventListener("DOMContentLoaded", () => {
  // Dark / Light Mode
  const toggleBtn = document.querySelector(".toggle-mode");
  toggleBtn?.addEventListener("click", () => {
    document.body.classList.toggle("dark");
    toggleBtn.textContent = document.body.classList.contains("dark")
      ? "☀️ Light Mode"
      : "🌙 Dark Mode";
  });

  // Cart elements
  const cartBox = document.getElementById("cart");
  const cartItems = document.getElementById("cart-items");
  const cartTotal = document.getElementById("cart-total");
  const checkoutForm = document.getElementById("checkout-form");
  const checkoutItems = document.getElementById("checkout-items");

  let cart = [];

  // Toggle cart visibility
  document.querySelector(".toggle-cart")?.addEventListener("click", () => {
    cartBox.classList.toggle("active");
  });

  // Add item to cart
  window.addToCart = function (id, name, price) {
    const item = cart.find(i => i.id === id);
    if (item) item.qty++;
    else cart.push({ id, name, price, qty: 1 });
    renderCart();
    cartBox.classList.add("active");
  };

  // Render cart items
  function renderCart() {
    cartItems.innerHTML = "";
    let total = 0;
    cart.forEach((item, index) => {
      total += item.price * item.qty;
      cartItems.innerHTML += `
        <div class="cart-item">
          <span>${item.name} x${item.qty}</span>
          <div class="cart-actions">
            <button class="qty-btn" onclick="decreaseQty(${index})">−</button>
            <button class="qty-btn" onclick="increaseQty(${index})">+</button>
            <button class="remove-btn" onclick="removeItem(${index})">✖</button>
          </div>
        </div>
      `;
    });
    cartTotal.textContent = total.toFixed(2);
  }

  // Cart actions
  window.increaseQty = index => { cart[index].qty++; renderCart(); };
  window.decreaseQty = index => {
    if (cart[index].qty > 1) cart[index].qty--;
    else cart.splice(index, 1);
    renderCart();
  };
  window.removeItem = index => { cart.splice(index, 1); renderCart(); };
  window.closeCart = () => cartBox.classList.remove("active");

  // Smooth Responsive Checkout
  window.openCheckout = function () {
    if (!cart.length) return alert("Cart is empty!");
    checkoutItems.innerHTML = "";
    let total = 0;

    cart.forEach(item => {
      total += item.price * item.qty;
      checkoutItems.innerHTML += `
        <p>${item.name} x${item.qty} - NRs.${(item.price*item.qty).toFixed(2)}</p>
        <input type="hidden" name="menu_ids[]" value="${item.id}">
        <input type="hidden" name="quantities[]" value="${item.qty}">
      `;
    });

    document.getElementById("checkout-total").textContent = total.toFixed(2);
    // checkoutForm.style.display = "block";
    checkoutForm.classList.add("active");
  };
  // Close Checkout
  window.closeCheckout = function() {
    checkoutForm.classList.remove("active");
  };

  

  window.closeCheckout = function () {
    checkoutForm.classList.remove("active");
  };


  // Mobile menu toggle
  window.toggleMobileMenu = function () {
    document.getElementById("category-bar").classList.toggle("active");
  };

  // Category filter
  const categoryButtons = document.querySelectorAll(".category-btn");
  const menuCards = document.querySelectorAll(".menu-card");

  categoryButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      const category = btn.dataset.category;
      categoryButtons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      menuCards.forEach(card => {
        card.style.display = category === "all" || card.dataset.category === category ? "block" : "none";
      });
    });
  });

  // Initial setup
  checkoutForm.classList.remove("active");
  renderCart();

  window.addEventListener("click", function(e) {
  if (cartBox.classList.contains("active") && !cartBox.contains(e.target) && !e.target.matches('.toggle-cart')) {
    cartBox.classList.remove("active");
  }
});
});