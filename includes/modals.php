<div id="authModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
      <button id="closeModalBtn" class="close-btn">&times;</button>
      <div id="loginForm">
        <h3>Welcome back</h3>
        <p class="modal-subtitle">Log in to shop local and support growth</p>
        <input type="email" id="loginEmail" placeholder="Email">
        <input type="password" id="loginPassword" placeholder="Password">
        <button id="loginSubmitBtn">Log in</button>
        <p id="loginError" class="form-error"></p>
        <p class="auth-toggle">New here? <a id="switchToRegister">Create an account</a></p>
      </div>
      <div id="registerForm" style="display:none;">
        <h3>Join SMEConnect</h3>
        <p class="modal-subtitle">Create an account to start shopping or selling</p>
        <input type="text" id="regName" placeholder="Name">
        <input type="email" id="regEmail" placeholder="Email">
        <input type="password" id="regPassword" placeholder="Password">
        <select id="regRole">
          <option value="buyer">Buyer</option>
          <option value="seller">Seller</option>
        </select>
        <button id="registerSubmitBtn">Register</button>
        <p id="registerError" class="form-error"></p>
        <p class="auth-toggle">Already have an account? <a id="switchToLogin">Log in</a></p>
      </div>
    </div>
  </div>