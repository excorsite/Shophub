  Menu Section
  <main class="flex-grow">
    <div class="max-w-6xl mx-auto px-6 py-10">
      <h1 class="text-3xl font-bold mb-8 text-center">🍽️ Our Menu</h1>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        <?php foreach ($products as $product): ?>
        <div class="card">
          <div class="card__shine"></div>
          <div class="card__glow"></div>
          <div class="card__content">
            <div class="card__badge">BUY</div>
            <div class="card__image" style="--bg-color: #a78bfa"></div>
            <div class="card__text">
              <p class="card__title"><?= htmlspecialchars($product['name']) ?></p>
              <p class="card__description"><?= htmlspecialchars($product['description']) ?></p>
            </div>
            <div class="card__footer">
              <div class="card__price">Rs. <?= number_format($product['price'], 2) ?></div>
              <form method="GET" class="flex items-center gap-2">
                <input type="hidden" name="add_to_cart" value="<?= $product['id'] ?>">
                <input type="number" name="quantity" value="1" min="1" class="w-12 text-center border rounded text-sm">
                <button type="submit" class="card__button" title="Add to Cart">
                  <svg height="16" width="16" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke="currentColor" d="M4 12H20M12 4V20" fill="currentColor"></path>
                  </svg>
                </button>
              </form>
            </div>
            <div class="mt-3 flex justify-between">
              <a href="cart.php?id=<?= $product['id'] ?>" class="text-gray-600 text-sm hover:underline">Cart</a>
              <a href="checkout.php?product_id=<?= $product['id'] ?>" class="text-blue-600 text-sm hover:underline">Buy Now</a>
              <a href="?add_to_wishlist=<?= $product['id'] ?>" class="text-yellow-500 text-sm hover:underline">Wishlist</a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
