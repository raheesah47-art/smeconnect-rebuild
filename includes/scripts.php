<script src="/smeconnect/assets/js/products.js"></script>

<script src="/smeconnect/assets/js/auth.js"></script>

<script>
document.getElementById('buyerDistrict').addEventListener('change', function() {
  const district = this.value;
  const row = document.getElementById('deliveryFeeRow');
  const feeEl = document.getElementById('deliveryFee');
  if (!district) {
    row.style.display = 'none';
    return;
  }
  fetch(`/smeconnect/api/delivery/get_delivery_fee.php?district=${encodeURIComponent(district)}`)
    .then(res => res.json())
    .then(data => {
      if (data.error) {
        feeEl.textContent = 'Delivery unavailable for this district';
        row.style.display = 'flex';
        return;
      }
      feeEl.textContent = data.fee === 0 ? 'Free delivery' : `Rs ${data.fee}`;
      row.style.display = 'flex';
    })
    .catch(() => {
      feeEl.textContent = 'Unable to calculate';
      row.style.display = 'flex';
    });
});
</script>