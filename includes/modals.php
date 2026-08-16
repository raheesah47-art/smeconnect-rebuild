<div id="authModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
      <button id="closeModalBtn" class="close-btn">&times;</button>
      <div id="loginForm">
        <h3>Log in</h3>
        <input type="email" id="loginEmail" placeholder="Email">
        <input type="password" id="loginPassword" placeholder="Password">
        <button id="loginSubmitBtn">Log in</button>
        <p id="loginError" class="form-error"></p>
      </div>
      <div id="registerForm" style="display:none;">
        <h3>Register</h3>
        <input type="text" id="regName" placeholder="Name">
        <input type="email" id="regEmail" placeholder="Email">
        <input type="password" id="regPassword" placeholder="Password">
        <select id="regRole">
          <option value="buyer">Buyer</option>
          <option value="seller">Seller</option>
        </select>
        <button id="registerSubmitBtn">Register</button>
        <p id="registerError" class="form-error"></p>
      </div>
    </div>
  </div>