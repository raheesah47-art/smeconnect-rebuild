const STATUS_STEPS = ['Placed', 'Confirmed', 'Out for delivery', 'Delivered'];

fetch('http://localhost/smeconnect/api/seller/get_seller_orders.php', { credentials: 'same-origin' })
  .then(res => res.json())
  .then(orders => {
    const container = document.getElementById('sellerOrdersList');

    if (orders.error) {
      container.innerHTML = `<p>${orders.error}</p>`;
      return;
    }
    if (orders.length === 0) {
      container.innerHTML = '<p>No orders yet.</p>';
      return;
    }

    orders.forEach(order => {
      const currentIndex = STATUS_STEPS.indexOf(order.current_status);
      const nextStatus = STATUS_STEPS[currentIndex + 1];

      const card = document.createElement('div');
      card.className = 'product-card';
      card.style.padding = '20px';
      card.style.marginBottom = '16px';
      card.innerHTML = `
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
          <strong>Order #${order.order_id}</strong>
          <span style="background:#E5F7F4; color:var(--color-teal-dark); padding:4px 12px; border-radius:100px; font-size:12px; font-weight:700;">
            ${order.current_status}
          </span>
        </div>
        <p style="color:#8b8578; font-size:13px; margin-bottom:4px;"><strong>${order.buyer_name || 'N/A'}</strong> · ${order.buyer_phone || 'N/A'}</p>
        <p style="color:#8b8578; font-size:13px; margin-bottom:12px;">${order.district}</p>
        <ul style="margin:0 0 16px; padding-left:18px; font-size:14px;">
          ${order.items.map(item => `<li>${item.product_name} × ${item.quantity} — Rs ${item.price * item.quantity}</li>`).join('')}
        </ul>
        ${nextStatus ? `<button class="advance-btn btn-pill-primary" data-order-id="${order.order_id}" data-next-status="${nextStatus}">Mark as ${nextStatus}</button>` : '<p style="color:var(--color-teal-dark); font-weight:600;">✓ Delivered</p>'}
      `;
      container.appendChild(card);
    });

    document.querySelectorAll('.advance-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        fetch('http://localhost/smeconnect/api/seller/update_order_status.php', {
          method: 'POST',
          credentials: 'same-origin',
          body: JSON.stringify({
            order_id: btn.dataset.orderId,
            status: btn.dataset.nextStatus
          })
        })
          .then(res => res.json())
          .then(result => {
            if (result.success) {
              location.reload();
            } else {
              alert(result.error);
            }
          });
      });
    });
  })
  .catch(err => console.error('Failed to load seller orders:', err));
