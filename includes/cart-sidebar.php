<aside id="cartSidebar" class="cart-sidebar">
    <div class="cart-header">
      <h3>My cart (<span id="cartCount">0</span>)</h3>
    </div>
    <div id="cartItems"></div>
    <div class="cart-totals">
      <div class="row"><span>Subtotal</span><span id="cartSubtotal">Rs 0</span></div>
    </div>

    <div class="checkout-details">
      <p class="checkout-details-title">Delivery details</p>
      <input type="text" id="buyerName" placeholder="Your full name">
      <input type="tel" id="buyerPhone" placeholder="Phone number (e.g. 5xxx xxxx)">
      <select id="buyerDistrict">
        <option value="">Select your district</option>
        <option>Port Louis</option>
        <option>Pamplemousses</option>
        <option>Riviere du Rempart</option>
        <option>Flacq</option>
        <option>Grand Port</option>
        <option>Savanne</option>
        <option>Plaines Wilhems</option>
        <option>Moka</option>
        <option>Black River</option>
      </select>
      <textarea id="buyerAddress" placeholder="Full delivery address (street, house number, locality)" rows="3"></textarea>
      <p id="checkoutError" class="form-error"></p>
    </div>

    <div id="paypal-button-container"></div>
  </aside>