<?php $active = ''; ?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

  <main>
    <div class="page-layout">
      <?php include 'includes/sidenav.php'; ?>

      <div class="main-content">
        <section id="productsSection">
          <h2 class="section-title" id="searchResultsTitle">Search results</h2>
          <div id="productGrid" class="product-grid"></div>
        </section>
      </div>

      <?php include 'includes/cart-sidebar.php'; ?>
    </div>
  </main>

  <?php include 'includes/modals.php'; ?>
  <?php include 'includes/scripts.php'; ?>
  <script>
    const searchParams = new URLSearchParams(window.location.search);
    const searchQuery = searchParams.get('q') || '';
    document.getElementById('searchResultsTitle').textContent = searchQuery
      ? `Results for "${searchQuery}"`
      : 'Search results';
    loadProducts(undefined, undefined, searchQuery);
  </script>
</body>
</html>
