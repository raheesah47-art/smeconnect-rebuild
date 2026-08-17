let selectedDistrict = null;
let currentDeliveryFee = 0;

document.querySelectorAll('.district-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.district-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    selectedDistrict = btn.dataset.district;

    fetch(`http://localhost/smeconnect/api/delivery/get_delivery_fee.php?district=${encodeURIComponent(selectedDistrict)}`)
      .then(res => res.json())
      .then(data => {
        currentDeliveryFee = data.fee;
        const display = document.getElementById('feeDisplay');
        display.textContent = data.fee === 0 ? 'Free delivery' : `Rs ${data.fee}`;
      });
  });
});