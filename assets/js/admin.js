const API_ADMIN = 'http://localhost/smeconnect/api/admin';

function loadUsers() {
  fetch(`${API_ADMIN}/get_all_users.php`, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(users => {
      if (users.error) {
        document.querySelector('.dash-main').innerHTML = `<p>${users.error}</p>`;
        return;
      }

      document.getElementById('adminStats').innerHTML = `
        <div class="stat-card">
          <div class="stat-value">${users.length}</div>
          <div class="stat-label">Total Users</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">${users.filter(u => u.role === 'seller').length}</div>
          <div class="stat-label">Sellers</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">${users.filter(u => u.role === 'buyer').length}</div>
          <div class="stat-label">Buyers</div>
        </div>
      `;

      document.getElementById('usersTable').innerHTML = `
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>District</th><th>Joined</th></tr></thead>
        <tbody>
          ${users.map(u => `
            <tr>
              <td>${u.name}</td>
              <td>${u.email}</td>
              <td><span class="status-pill status-confirmed">${u.role}</span></td>
              <td>${u.district || '—'}</td>
              <td>${new Date(u.created_at).toLocaleDateString()}</td>
            </tr>
          `).join('')}
        </tbody>
      `;
    });
}

function loadProducts() {
  fetch(`${API_ADMIN}/get_all_products.php`, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(products => {
      document.getElementById('productsTable').innerHTML = `
        <thead><tr><th>Name</th><th>Seller</th><th>Category</th><th>Price</th><th>Trust</th><th></th></tr></thead>
        <tbody>
          ${products.map(p => `
            <tr>
              <td>${p.name}</td>
              <td>${p.seller_name || '—'}</td>
              <td>${p.category}</td>
              <td>Rs ${p.price}</td>
              <td>${p.trust_score}</td>
              <td><button class="admin-delete-product" data-id="${p.id}" style="color:#c0392b; background:none; border:none; cursor:pointer; font-weight:600;">Remove</button></td>
            </tr>
          `).join('')}
        </tbody>
      `;

      document.querySelectorAll('.admin-delete-product').forEach(btn => {
        btn.addEventListener('click', () => {
          if (!confirm('Remove this product from the platform?')) return;
          fetch(`${API_ADMIN}/delete_product.php`, {
            method: 'POST', credentials: 'same-origin',
            body: JSON.stringify({ id: btn.dataset.id })
          }).then(() => loadProducts());
        });
      });
    });
}

function loadOrders() {
  fetch(`${API_ADMIN}/get_all_orders.php`, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(orders => {
      document.getElementById('ordersTable').innerHTML = `
        <thead><tr><th>Order ID</th><th>District</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
          ${orders.map(o => `
            <tr>
              <td>#${o.id}</td>
              <td>${o.district}</td>
              <td>Rs ${o.total}</td>
              <td><span class="status-pill status-${(o.current_status || 'placed').toLowerCase().replace(/\s/g,'-')}">${o.current_status || 'Placed'}</span></td>
              <td>${new Date(o.created_at).toLocaleDateString()}</td>
            </tr>
          `).join('')}
        </tbody>
      `;
    });
}

loadUsers();
loadProducts();
loadOrders();