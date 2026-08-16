const API_BASE = 'http://localhost/smeconnect/api';
const STATUS_STEPS = ['Placed', 'Confirmed', 'Out for delivery', 'Delivered'];

fetch(`${API_BASE}/orders/get_my_orders.php`, { credentials: 'same-origin' })
  .then(res => res.json())
  .then(orders => {
    const container = document.getElementById('ordersList');
    if (orders.error) {
      container.innerHTML = `<p>${orders.error}</p>`;
      return;
    }
    if (orders.length === 0) {
      container.innerHTML = '<p>No orders yet.</p>';
      return;
    }

    orders.forEach(order => {
      const currentStatus = order.status_log.length
        ? order.status_log[order.status_log.length - 1].status
        : 'Placed';
      const currentIndex = STATUS_STEPS.indexOf(currentStatus);

      const card = document.createElement('div');
      card.style.cssText = 'background:white;border-radius:12px;padding:20px;margin-bottom:16px;';
      card.innerHTML = `
        <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
          <strong>Order #${order.id}</strong>
          <span>Rs ${order.total}</span>
        </div>
        <div style="display:flex;gap:8px;margin-bottom:12px;">
          ${STATUS_STEPS.map((step, i) => `
            <div style="flex:1;text-align:center;">
              <div style="width:24px;height:24px;border-radius:50%;margin:0 auto 4px;
                background:${i <= currentIndex ? 'var(--color-teal)' : '#ddd'};
                color:white;display:flex;align-items:center;justify-content:center;font-size:12px;">
                ${i <= currentIndex ? '✓' : ''}
              </div>
              <span style="font-size:11px;">${step}</span>
            </div>
          `).join('')}
        </div>
        <ul style="margin:0;padding-left:18px;">
          ${order.items.map(item => `<li>${item.product_name} × ${item.quantity} — Rs ${item.price * item.quantity}</li>`).join('')}
        </ul>
      `;
      container.appendChild(card);
    });
  });