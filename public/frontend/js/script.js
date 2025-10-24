
document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.querySelector(".toggle-mode");
  toggleBtn?.addEventListener("click", () => {
    document.body.classList.toggle("dark");
    toggleBtn.textContent = document.body.classList.contains("dark") ? "☀️ Light Mode" : "🌙 Dark Mode";
  });

  const cartBox = document.getElementById("cart");
  const cartItems = document.getElementById("cart-items");
  const cartTotal = document.getElementById("cart-total");
  const checkoutForm = document.getElementById("checkout-form");
  const checkoutItems = document.getElementById("checkout-items");

  let cart = [];

  const cartBtn = document.querySelector(".toggle-cart");
  cartBtn?.addEventListener("click", () => {
    cartBox.classList.toggle("active");
  });

  window.addToCart = function (id, name, price) {
    const item = cart.find(i => i.id === id);
    if (item) {
      item.qty++;
    } else {
      cart.push({ id, name, price, qty: 1 });
    }
    renderCart();
    cartBox.classList.add("active");
  };

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

  window.increaseQty = function (index) {
    cart[index].qty++;
    renderCart();
  };

  window.decreaseQty = function (index) {
    if (cart[index].qty > 1) {
      cart[index].qty--;
    } else {
      cart.splice(index, 1);
    }
    renderCart();
  };

  window.removeItem = function (index) {
    cart.splice(index, 1);
    renderCart();
  };

  window.openCheckout = function () {
    if (cart.length === 0) return alert("Cart is empty!");
    checkoutItems.innerHTML = "";
    let total = 0;
    cart.forEach(item => {
      total += item.price * item.qty;
      checkoutItems.innerHTML += `<p>${item.name} x${item.qty} - NRs.${(item.price * item.qty).toFixed(2)}</p>`;
      checkoutItems.innerHTML += `
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


  window.toggleMobileMenu = function () {
    const menu = document.getElementById("category-bar");
    menu.classList.toggle("active");
  };

  checkoutForm.style.display = "none";
  renderCart();
  
const categoryButtons = document.querySelectorAll(".category-btn");
const menuCards = document.querySelectorAll(".menu-card");

categoryButtons.forEach(btn => {
  btn.addEventListener("click", () => {
    const category = btn.dataset.category;

    categoryButtons.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");

    menuCards.forEach(card => {
      if (category === "all" || card.dataset.category === category) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    });
  });
});
});

