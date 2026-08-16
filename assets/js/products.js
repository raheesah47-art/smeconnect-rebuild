const API_PRODUCTS = 'http://localhost/smeconnect/api/products';
const API_CART = 'http://localhost/smeconnect/api/cart';
const API_PAYMENTS = 'http://localhost/smeconnect/api/payments';

function loadProducts(overrideFilter, overrideCategory) {
  const params = new URLSearchParams(window.location.search);
  const filter = overrideFilter !== undefined ? overrideFilter : (params.get('filter') || '');
  const category = overrideCategory !== undefined ? overrideCategory : (params.get('category') || '');

  let url = `${API_PRODUCTS}/get_products.php?filter=${filter}&category=${encodeURIComponent(category)}`;

  fetch(url, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(products => {
      const grid = document.getElementById('productGrid');
      grid.innerHTML = '';

      const titleEl = document.querySelector('#productsSection .section-title');
      const titles = { deals: 'Deals', new: 'New arrivals', bestsellers: 'Best sellers' };
      titleEl.textContent = category ? category : (titles[filter] || 'Best deals for you');

      if (products.length === 0) {
        grid.innerHTML = '<p>No products found.</p>';
        return;
      }

      products.forEach(p => {
        const discountPct = p.original_price
          ? Math.round(100 - (p.price / p.original_price) * 100)
          : null;

        const card = document.createElement('div');
        card.className = 'product-card';
       card.innerHTML = `
  <a href="/smeconnect/product.php?id=${p.id}" style="text-decoration:none; color:inherit;">
  <div class="card-tile">
            ${discountPct ? `<span class="badge-discount">-${discountPct}%</span>` : ''}
            <span class="badge-verified">${p.trust_score} Verified</span>
            <button class="wishlist-btn" data-id="${p.id}">♡</button>
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
  </a>
`;
        grid.appendChild(card);
      });

      document.querySelectorAll('.add-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          addToCart(btn.dataset.id);
        });
      });
      document.querySelectorAll('.wishlist-btn').forEach(btn => {
        btn.addEventListener('click', () => toggleWishlist(btn.dataset.id, btn));
      });
    })
    .catch(err => console.error('loadProducts failed:', err));
}

function addToCart(productId) {
  fetch(`${API_CART}/add_to_cart.php`, {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify({ product_id: productId })
  })
    .then(res => res.json())
    .then(() => loadCart())
    .catch(err => console.error('addToCart failed:', err));
}

function updateCartQty(cartItemId, quantity) {
  fetch(`${API_CART}/update_cart.php`, {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify({ cart_item_id: cartItemId, quantity })
  })
    .then(res => res.json())
    .then(() => loadCart())
    .catch(err => console.error('updateCartQty failed:', err));
}

function removeFromCart(cartItemId) {
  fetch(`${API_CART}/remove_from_cart.php`, {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify({ cart_item_id: cartItemId })
  })
    .then(res => res.json())
    .then(() => loadCart())
    .catch(err => console.error('removeFromCart failed:', err));
}

function loadCart() {
  fetch(`${API_CART}/get_cart.php`, { credentials: 'same-origin' })
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
    })
    .catch(err => console.error('loadCart failed:', err));
}

function toggleWishlist(productId, btn) {
  fetch(`http://localhost/smeconnect/api/wishlist/toggle_wishlist.php`, {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify({ product_id: productId })
  })
    .then(res => res.json())
    .then(result => {
      if (result.error) {
        alert(result.error);
        return;
      }
      btn.textContent = result.wishlisted ? '♥' : '♡';
    })
    .catch(err => console.error('toggleWishlist failed:', err));
}

function loadMakers() {
  const makers = [
    { initials: 'AC', name: 'Atelier Coco', location: 'Grand Baie', trust: 98, tag: 'Textiles', color: '#E85D45' },
    { initials: 'FB', name: 'Ferme Bois Chéri', location: 'Moka', trust: 95, tag: 'Produce', color: '#3FA88C' },
    { initials: 'KD', name: 'Karo Design', location: 'Curepipe', trust: 91, tag: 'Home', color: '#D9A441' }
  ];
  const grid = document.getElementById('makersGrid');
  if (!grid) return;
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

loadCart();
loadMakers();

if (typeof paypal !== 'undefined') {
  paypal.Buttons({
    createOrder: function () {
      return fetch(`${API_PAYMENTS}/paypal_create_order.php`, {
        method: 'POST',
        credentials: 'same-origin'
      })
        .then(res => res.json())
        .then(data => data.id);
    },
    onApprove: function (data) {
      return fetch(`${API_PAYMENTS}/paypal_capture_order.php`, {
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
}