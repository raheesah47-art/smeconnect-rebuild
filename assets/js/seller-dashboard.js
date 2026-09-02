const API_BASE = 'http://localhost/smeconnect/api/seller';

function loadDashboardStats() {
  fetch(`${API_BASE}/get_dashboard_stats.php`, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(stats => {
      if (stats.error) {
        document.querySelector('.dash-main').innerHTML = `<p>${stats.error} — <a href="index.php">log in as a seller</a> first.</p>`;
        return;
      }

      document.getElementById('greeting').textContent = `Welcome back, ${stats.seller_name}. Here's what's happening with your store.`;

      const statGrid = document.getElementById('statGrid');
      statGrid.innerHTML = `
        <div class="stat-card">
          <div class="stat-icon" style="background:#E5F7F4; color:var(--color-teal-dark);">🛍</div>
          <div class="stat-value">${stats.product_count}</div>
          <div class="stat-label">Total Products</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:#FCE9E4; color:var(--color-coral-dark);">🧾</div>
          <div class="stat-value">${stats.order_count}</div>
          <div class="stat-label">Orders</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:#FBEEDA; color:var(--color-gold-dark);">💰</div>
          <div class="stat-value">Rs ${stats.total_sales.toFixed(0)}</div>
          <div class="stat-label">Total Sales</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:#F1E9FB; color:#7A4FD6;">♡</div>
          <div class="stat-value">${stats.wishlist_count}</div>
          <div class="stat-label">Wishlisted</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:#E5F7F4; color:var(--color-teal-dark);">★</div>
          <div class="stat-value">${stats.avg_trust}</div>
          <div class="stat-label">Trust Score</div>
        </div>
      `;

      const table = document.getElementById('ordersTable');
      if (stats.recent_orders.length === 0) {
        table.innerHTML = '<p style="color:#8b8578;">No orders yet.</p>';
      } else {
           table.innerHTML = `
          <thead>
            <tr><th>Order ID</th><th>Buyer</th><th>Total</th><th>Status</th><th>Date</th></tr>
          </thead>
          <tbody>
            ${stats.recent_orders.map(o => `
              <tr>
                <td>#${o.id}</td>
                <td>${o.buyer_name || 'N/A'}<br><span style="color:#8b8578; font-size:12px;">${o.buyer_phone || ''}</span></td>
                <td>Rs ${o.total}</td>
                <td><span class="status-pill status-${(o.status || 'placed').toLowerCase().replace(/\s/g, '-')}">${o.status || 'Placed'}</span></td>
                <td>${new Date(o.created_at).toLocaleDateString()}</td>
              </tr>
            `).join('')}
          </tbody>
        `;
      }
    });
}

function loadMyProducts() {
  fetch(`${API_BASE}/get_my_products.php`, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(products => {
      if (products.error) return;
      const list = document.getElementById('myProductsList');
      list.innerHTML = '';
      products.forEach(p => {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.innerHTML = `
          <div class="card-tile"><img src="${p.image_url || 'https://placehold.co/400x400/CCCCCC/FFFFFF?text=No+Image'}" alt="${p.name}" class="card-image"></div>
          <div class="card-body">
            <p class="category">${p.category}</p>
            <h4>${p.name}</h4>
            <p class="seller">${p.district}</p>
            <div class="price-row"><span class="price">Rs ${p.price}</span></div>
            <div style="display:flex; gap:8px; margin-top:10px;">
              <button class="edit-btn" data-id="${p.id}" style="flex:1; padding:8px; border-radius:8px; border:1px solid var(--color-line); background:white; cursor:pointer;">Edit</button>
              <button class="delete-btn" data-id="${p.id}" style="flex:1; padding:8px; border-radius:8px; border:1px solid #f5c6c6; background:#fff5f5; color:#c0392b; cursor:pointer;">Delete</button>
            </div>
          </div>
        `;
        list.appendChild(card);
      });

      document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => openEditForm(products.find(p => p.id == btn.dataset.id)));
      });
      document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => deleteProduct(btn.dataset.id));
      });
    });
}

function openEditForm(product) {
  document.getElementById('productForm').style.display = 'block';
  document.getElementById('formTitle').textContent = 'Edit Product';
  document.getElementById('editProductId').value = product.id;
  document.getElementById('pName').value = product.name;
  document.getElementById('pCategory').value = product.category;
  document.getElementById('pDistrict').value = product.district;
  document.getElementById('pPrice').value = product.price;
  document.getElementById('pOriginalPrice').value = product.original_price || '';
  const preview = document.getElementById('pImagePreview');
  if (product.image_url) {
    preview.src = product.image_url;
    preview.style.display = 'block';
  } else {
    preview.style.display = 'none';
  }

}

function deleteProduct(id) {
  if (!confirm('Delete this product?')) return;
  fetch(`${API_BASE}/delete_product.php`, {
    method: 'POST', credentials: 'same-origin', body: JSON.stringify({ id })
  }).then(() => { loadMyProducts(); loadDashboardStats(); });
}

  document.getElementById('showAddFormBtn').addEventListener('click', () => {
  document.getElementById('productForm').style.display = 'block';
  document.getElementById('formTitle').textContent = 'Add Product';
  document.getElementById('editProductId').value = '';
  ['pName','pCategory','pDistrict','pPrice','pOriginalPrice'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('pImageFile').value = '';
  document.getElementById('pImagePreview').style.display = 'none';
});

document.getElementById('pImageFile').addEventListener('change', (e) => {
  const file = e.target.files[0];
  if (!file) return;
  const preview = document.getElementById('pImagePreview');
  preview.src = URL.createObjectURL(file);
  preview.style.display = 'block';
});

document.getElementById('cancelFormBtn').addEventListener('click', () => {
  document.getElementById('productForm').style.display = 'none';
});

document.getElementById('saveProductBtn').addEventListener('click', () => {
  const id = document.getElementById('editProductId').value;
  const formData = new FormData();
  formData.append('name', document.getElementById('pName').value);
  formData.append('category', document.getElementById('pCategory').value);
  formData.append('district', document.getElementById('pDistrict').value);
  formData.append('price', document.getElementById('pPrice').value);
  formData.append('original_price', document.getElementById('pOriginalPrice').value || '');
  const imageFile = document.getElementById('pImageFile').files[0];
  if (imageFile) formData.append('image', imageFile);
  if (id) formData.append('id', id);

  const endpoint = id ? 'update_product.php' : 'add_product.php';

  fetch(`${API_BASE}/${endpoint}`, {
    method: 'POST', credentials: 'same-origin', body: formData
  })
    .then(res => res.json())
    .then(result => {
      if (result.error) { alert(result.error); return; }
      document.getElementById('productForm').style.display = 'none';
      loadMyProducts();
      loadDashboardStats();
    });
});
document.getElementById('sellerProfileFile').addEventListener('change', (e) => {
  const file = e.target.files[0];
  if (!file) return;

  const preview = document.getElementById('sellerProfilePreview');
  preview.src = URL.createObjectURL(file);
  preview.style.display = 'block';

  const formData = new FormData();
  formData.append('image', file);

  fetch(`${API_BASE}/upload_profile_image.php`, {
    method: 'POST', credentials: 'same-origin', body: formData
  })
    .then(res => res.json())
    .then(result => {
      if (result.error) { alert(result.error); return; }
      alert('Profile picture updated!');
    });
});

loadDashboardStats();
loadMyProducts();