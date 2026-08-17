<?php $active = ''; ?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

  <main>
    <div class="dash-layout">
      <aside class="dash-nav">
        <a href="admin.php" class="nav-item active">🛡 Admin</a>
        <a href="index.php" class="nav-item">← Back to shop</a>
      </aside>

      <div class="dash-main">
        <h1 class="dash-title">Admin Panel</h1>
        <p class="dash-sub">Platform-wide overview and moderation</p>

        <div class="stat-grid" id="adminStats"></div>

        <h2 class="section-title" style="padding:0; margin-top:32px;">All Users</h2>
        <table class="orders-table" id="usersTable" style="margin-bottom:32px;"></table>

        <h2 class="section-title" style="padding:0;">All Products</h2>
        <table class="orders-table" id="productsTable" style="margin-bottom:32px;"></table>

        <h2 class="section-title" style="padding:0;">All Orders</h2>
        <table class="orders-table" id="ordersTable"></table>
      </div>
    </div>
  </main>

  <?php include 'includes/modals.php'; ?>
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/scripts.php'; ?>
  <script src="/smeconnect/assets/js/admin.js"></script>
</body>
</html>