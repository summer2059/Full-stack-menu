document.addEventListener("DOMContentLoaded", () => {
  // ======================
  // CART DATA & ELEMENTS
  // ======================
  let cart = [];
  const cartBox = document.getElementById("cart");
  const cartItems = document.getElementById("cart-items");
  const cartTotal = document.getElementById("cart-total");

  // ======================
  // ADD TO CART
  // ======================
  // ✅ FIXED — use real id
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


  // ======================
  // RENDER CART
  // ======================
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

  // ======================
  // QUANTITY & REMOVE
  // ======================
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

  // ======================
  // CHECKOUT
  // ======================
  const checkoutForm = document.getElementById("checkout-form");
  const checkoutItems = document.getElementById("checkout-items");
  const checkoutTotal = document.getElementById("checkout-total");

  window.openCheckout = function () {
    if (cart.length === 0) return alert("Cart is empty!");

    checkoutItems.innerHTML = "";
    let total = 0;

    cart.forEach(item => {
      total += item.price * item.qty;

      // Summary display
      checkoutItems.innerHTML += `<p>${item.name} x${item.qty} - NRs.${(item.price * item.qty).toFixed(2)}</p>`;

      // Hidden inputs for backend
      checkoutItems.innerHTML += `
        <input type="hidden" name="menu_ids[]" value="${item.id}">
        <input type="hidden" name="quantities[]" value="${item.qty}">
      `;
    });

    checkoutTotal.textContent = total.toFixed(2);
    checkoutForm.style.display = "block";
  };

  window.closeCheckout = function () {
    checkoutForm.style.display = "none";
  };

  // ======================
  // CLOSE CART
  // ======================
  window.closeCart = function () {
    cartBox.classList.remove("active");
  };

  // ======================
  // INIT
  // ======================
  renderCart();
});
