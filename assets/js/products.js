const API_BASE = 'http://localhost/smeconnect/api';

function loadProducts() {
  fetch(`${API_BASE}/get_products.php`, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(products => {
      const grid = document.getElementById('productGrid');
      grid.innerHTML = '';
      products.forEach(p => {
        const discountPct = p.original_price
          ? Math.round(100 - (p.price / p.original_price) * 100)
          : null;

        const card = document.createElement('div');
        card.className = 'product-card';
        card.innerHTML = `
          <div class="card-tile">
            ${discountPct ? `<span class="badge-discount">-${discountPct}%</span>` : ''}
            <span class="badge-verified">${p.trust_score} Verified</span>
          </div>
          <div class="card-body">
            <p class="category">${p.category}</p>
            <h4>${p.name}</h4>
            <p class="seller">by ${p.seller_name} · ${p.district}</p>
            <div class="price-row">
              <span class="price">Rs ${p.price}</span>
              ${p.original_price ? `<span class="original-price">Rs ${p.original_price}</span>` : ''}
              <button class="add-btn" data-id="${p.id}">+</button>
            </div>
          </div>
        `;
        grid.appendChild(card);
      });

      // Wire up every "+" button just created
      document.querySelectorAll('.add-btn').forEach(btn => {
        btn.addEventListener('click', () => addToCart(btn.dataset.id));
      });
    });
}

function addToCart(productId) {
  fetch(`${API_BASE}/add_to_cart.php`, {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify({ product_id: productId })
  })
    .then(res => res.json())
    .then(() => loadCart());
}

function updateCartQty(cartItemId, quantity) {
  fetch(`${API_BASE}/update_cart.php`, {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify({ cart_item_id: cartItemId, quantity })
  })
    .then(res => res.json())
    .then(() => loadCart());
}

function removeFromCart(cartItemId) {
  fetch(`${API_BASE}/remove_from_cart.php`, {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify({ cart_item_id: cartItemId })
  })
    .then(res => res.json())
    .then(() => loadCart());
}

function loadCart() {
  fetch(`${API_BASE}/get_cart.php`, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(items => {
      const container = document.getElementById('cartItems');
      const countEl = document.getElementById('cartCount');
      const subtotalEl = document.getElementById('cartSubtotal');

      container.innerHTML = '';
      let subtotal = 0;
      let totalQty = 0;

      items.forEach(item => {
        subtotal += item.price * item.quantity;
        totalQty += item.quantity;

        const row = document.createElement('div');
        row.className = 'cart-item';
        row.innerHTML = `
          <div>
            <p class="cart-item-name">${item.name}</p>
            <p class="cart-item-district">${item.district}</p>
          </div>
          <div class="qty-controls">
            <button class="qty-btn minus">-</button>
            <span>${item.quantity}</span>
            <button class="qty-btn plus">+</button>
          </div>
          <span class="cart-item-price">Rs ${item.price * item.quantity}</span>
        `;

        row.querySelector('.minus').addEventListener('click', () => {
          if (item.quantity <= 1) {
            removeFromCart(item.cart_item_id);
          } else {
            updateCartQty(item.cart_item_id, item.quantity - 1);
          }
        });
        row.querySelector('.plus').addEventListener('click', () => {
          updateCartQty(item.cart_item_id, item.quantity + 1);
        });

        container.appendChild(row);
      });

      countEl.textContent = totalQty;
      subtotalEl.textContent = `Rs ${subtotal.toFixed(2)}`;
    });
}

loadProducts();
loadCart();

paypal.Buttons({
  createOrder: function () {
    return fetch(`${API_BASE}/paypal_create_order.php`, {
      method: 'POST',
      credentials: 'same-origin'
    })
      .then(res => res.json())
      .then(data => data.id); // PayPal's order ID
  },
  onApprove: function (data) {
    return fetch(`${API_BASE}/paypal_capture_order.php`, {
      method: 'POST',
      credentials: 'same-origin',
      body: JSON.stringify({ orderID: data.orderID })
    })
      .then(res => res.json())
      .then(result => {
        if (result.success) {
          alert(`Payment successful! Order #${result.order_id} placed.`);
          loadCart();
        } else {
          alert('Payment could not be completed.');
        }
      });
  }
}).render('#paypal-button-container');

function loadMakers() {
  const makers = [
    { initials: 'AC', name: 'Atelier Coco', location: 'Grand Baie', trust: 98, tag: 'Textiles', color: '#E85D45' },
    { initials: 'FB', name: 'Ferme Bois Chéri', location: 'Moka', trust: 95, tag: 'Produce', color: '#3FA88C' },
    { initials: 'KD', name: 'Karo Design', location: 'Curepipe', trust: 91, tag: 'Home', color: '#D9A441' }
  ];
  const grid = document.getElementById('makersGrid');
  grid.innerHTML = makers.map(m => `
    <div class="maker-card">
      <div class="maker-avatar" style="background:${m.color}">${m.initials}</div>
      <div class="maker-info">
        <h4>${m.name}</h4>
        <p>${m.location} · Trust ${m.trust}</p>
        <div class="maker-tags"><span>${m.tag}</span></div>
      </div>
    </div>
  `).join('');
}
loadMakers();