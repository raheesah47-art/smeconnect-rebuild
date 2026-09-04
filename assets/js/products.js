const API_PRODUCTS = 'http://localhost/smeconnect/api/products';
const API_CART = 'http://localhost/smeconnect/api/cart';
const API_PAYMENTS = 'http://localhost/smeconnect/api/payments';
const API_WISHLIST = 'http://localhost/smeconnect/api/wishlist';
const API_MAKERS = 'http://localhost/smeconnect/api/makers';

function loadProducts(overrideFilter, overrideCategory, overrideSearch) {
  const params = new URLSearchParams(window.location.search);
  const filter = overrideFilter !== undefined ? overrideFilter : (params.get('filter') || '');
  const category = overrideCategory !== undefined ? overrideCategory : (params.get('category') || '');
  const search = overrideSearch !== undefined ? overrideSearch : '';

  let url = `${API_PRODUCTS}/get_products.php?filter=${filter}&category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}`;

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
            <img src="${p.image_url || 'https://placehold.co/400x400/CCCCCC/FFFFFF?text=No+Image'}" alt="${p.name}" class="card-image">
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
          <img src="${item.image_url || 'https://placehold.co/80x80/CCCCCC/FFFFFF?text=No+Image'}" alt="${item.name}" class="cart-item-image">
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
  fetch(`${API_WISHLIST}/toggle_wishlist.php`, {
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
  const grid = document.getElementById('makersGrid');
  if (!grid) return;

  const colors = ['#E85D45', '#3FA88C', '#D9A441', '#2E4A62', '#6B9E3F'];

        fetch(`${API_MAKERS}/get_makers.php`)
    .then(res => res.json())
    .then(makers => {
      if (!makers.length) {
        grid.innerHTML = '<p style="color:#8b8578;">No local makers yet — be the first to sell!</p>';
        return;
      }
      const limit = grid.dataset.limit ? parseInt(grid.dataset.limit, 10) : makers.length;
      const limitedMakers = makers.slice(0, limit);
            grid.innerHTML = limitedMakers.map((m, i) => {
        const initials = m.seller_name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
        const color = colors[i % colors.length];
        const avatarContent = m.profile_image
          ? `<img src="${m.profile_image}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">`
          : initials;
        return `
          <div class="maker-card">
            <div class="maker-avatar" style="background:${color}">${avatarContent}</div>
            <div class="maker-info">
              <h4>${m.seller_name}</h4>
              <p>${m.district || 'Mauritius'} · Trust ${m.avg_trust}</p>
              <div class="maker-tags"><span>${m.top_category || 'General'}</span></div>
            </div>
          </div>
        `;
      }).join('');
    });
}

loadCart();
loadMakers();

if (typeof paypal !== 'undefined') {
  paypal.Buttons({
    onClick: function (data, actions) {
      const name = document.getElementById('buyerName').value.trim();
      const phone = document.getElementById('buyerPhone').value.trim();
      const district = document.getElementById('buyerDistrict').value;
      const address = document.getElementById('buyerAddress').value.trim();
      const errorEl = document.getElementById('checkoutError');

      const namePattern = /^[A-Za-z\s]{3,}$/;
      const phonePattern = /^(\+230)?\s?\d{7,8}$/;

      if (!name || !phone || !district || !address) {
        errorEl.textContent = 'Please fill in your name, phone number, district, and address before paying.';
        return actions.reject();
      }
      if (!namePattern.test(name)) {
        errorEl.textContent = 'Please enter a valid name (letters only, at least 3 characters).';
        return actions.reject();
      }
      if (!phonePattern.test(phone)) {
        errorEl.textContent = 'Please enter a valid Mauritius phone number (7-8 digits).';
        return actions.reject();
      }
      if (address.length < 10) {
        errorEl.textContent = 'Please enter a more complete delivery address.';
        return actions.reject();
      }
      errorEl.textContent = '';
      return actions.resolve();
    },
    createOrder: function () {
      return fetch(`${API_PAYMENTS}/paypal_create_order.php`, {
        method: 'POST',
        credentials: 'same-origin'
      })
        .then(res => res.json())
        .then(data => data.id);
    },
    onApprove: function (data) {
      const name = document.getElementById('buyerName').value.trim();
      const phone = document.getElementById('buyerPhone').value.trim();
      const district = document.getElementById('buyerDistrict').value;
      const address = document.getElementById('buyerAddress').value.trim();

      return fetch(`${API_PAYMENTS}/paypal_capture_order.php`, {
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({
          orderID: data.orderID,
          buyer_name: name,
          buyer_phone: phone,
          district: district,
          delivery_address: address
        })
      })
        .then(res => res.json())
        .then(result => {
          if (result.success) {
            alert(`Payment successful! Order #${result.order_id} placed.`);
            document.getElementById('buyerName').value = '';
            document.getElementById('buyerPhone').value = '';
            document.getElementById('buyerDistrict').value = '';
            document.getElementById('buyerAddress').value = '';
            loadCart();
          } else {
            alert('Payment could not be completed.');
          }
        });
    }
  }).render('#paypal-button-container');
}