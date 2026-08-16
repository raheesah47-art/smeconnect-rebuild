fetch(`http://localhost/smeconnect/api/wishlist/get_wishlist.php`, { credentials: 'same-origin' })
  .then(res => res.json())
  .then(products => {
    const grid = document.getElementById('productGrid');

    if (products.error) {
      grid.innerHTML = `<p>${products.error} — <a href="/smeconnect/index.php">go back and log in</a>.</p>`;
      return;
    }
    if (products.length === 0) {
      grid.innerHTML = '<p>Your wishlist is empty. Browse products and tap ♡ to save them here.</p>';
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
  })
  .catch(err => console.error('Failed to load wishlist:', err));