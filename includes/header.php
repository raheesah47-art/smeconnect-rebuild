<header class="topbar">
    <a href="/smeconnect/index.php" class="logo">
      <div class="logo-mark gloss"><span>S</span></div>
      <span>SME<span class="brand-accent">Connect</span></span>
    </a>

    <form class="search" id="searchForm">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="searchInput" placeholder="Search local SMEs, products, services…" autocomplete="off">
    </form>

    <div class="top-actions">
      <a href="/smeconnect/wishlist.php" class="icon-btn" title="Wishlist">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 1 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
      </a>

      <div id="authSection">
        <div id="loggedOutView">
          <button id="showLoginBtn">Log in</button>
          <button id="showRegisterBtn">Register</button>
        </div>
        <div id="loggedInView" class="user-chip" style="display:none;">
          <div class="avatar gloss" id="userAvatarInitials"></div>
          <div>
            <div class="name" id="welcomeMsg"></div>
            <a href="/smeconnect/seller-dashboard.html" id="dashboardLink" style="display:none; font-size:11px; color:var(--color-teal);">Seller Dashboard</a>
          </div>
          <button id="logoutBtn" style="margin-left:8px;">Log out</button>
        </div>
      </div>
    </div>
  </header>