<?php
include '../includes/db.php';
include '../includes/functions.php';
if (!isBuyer()) redirect('../login.php');

// Cache file settings
$cache_dir = '../cache';
$cache_file = $cache_dir . '/user_item_matrix.pkl';
$cache_time = 3600; // Cache for 1 hour

// Ensure cache directory exists and is writable
if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0755, true);
}
if (!is_writable($cache_dir)) {
    error_log("Cache directory ($cache_dir) is not writable.");
}

// Function to build the user-item matrix
function buildUserItemMatrix($pdo, $cache_dir, $cache_file, $cache_time) {
    // Check if valid cache exists
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
        $cached = @unserialize(file_get_contents($cache_file));
        if ($cached !== false) {
            return $cached;
        }
    }
    
    $matrix = [];
    $buyers = $pdo->query("SELECT id FROM users WHERE type='buyer' AND approved=1")->fetchAll(PDO::FETCH_COLUMN);
    $products = $pdo->query("SELECT id FROM products")->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($buyers) || empty($products)) {
        error_log("No buyers or products found for matrix construction. Buyers: " . count($buyers) . ", Products: " . count($products));
        return [];
    }
    
    foreach ($buyers as $buyer_id) {
        $stmt = $pdo->prepare("SELECT DISTINCT product_id FROM orders WHERE buyer_id = ? AND status != 'rejected'");
        $stmt->execute([$buyer_id]);
        $purchased = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $stmt = $pdo->prepare("SELECT DISTINCT product_id FROM cart WHERE buyer_id = ?");
        $stmt->execute([$buyer_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $items = array_unique(array_merge($purchased, $cart_items));
        
        $vector = array_fill_keys($products, 0);
        foreach ($items as $pid) {
            if (in_array($pid, $products)) {
                $vector[$pid] = 1;
            }
        }
        $matrix[$buyer_id] = $vector;
    }
    
    // Save to cache
    if (is_writable($cache_dir)) {
        if (@file_put_contents($cache_file, serialize($matrix)) === false) {
            error_log("Failed to write cache file: $cache_file");
        }
    } else {
        error_log("Cannot write to cache file: $cache_file (directory not writable)");
    }
    
    return $matrix;
}

// Function to calculate cosine similarity
function cosineSimilarity($vecA, $vecB) {
    $dot = 0;
    $normA = 0;
    $normB = 0;
    foreach ($vecA as $key => $val) {
        $dot += $val * $vecB[$key];
        $normA += $val * $val;
        $normB += $vecB[$key] * $vecB[$key];
    }
    $normA = sqrt($normA);
    $normB = sqrt($normB);
    if ($normA == 0 || $normB == 0) {
        return 0;
    }
    return $dot / ($normA * $normB);
}

// Function to get recommendations
function getRecommendations($pdo, $user_id, $matrix, $N = 5, $M = 5) {
    // Fallback if matrix is empty or user not in matrix
    if (empty($matrix) || !isset($matrix[$user_id])) {
        error_log("Empty matrix or user $user_id not in matrix. Falling back to popular products.");
        $popular = $pdo->query("
            SELECT p.id, p.name, p.description, p.price, p.image, COUNT(o.id) as order_count
            FROM products p
            LEFT JOIN orders o ON p.id = o.product_id AND o.status != 'rejected'
            GROUP BY p.id
            ORDER BY order_count DESC, p.created_at DESC
            LIMIT $M
        ")->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($prod) {
            return array_merge($prod, ['score' => $prod['order_count']]);
        }, $popular);
    }
    
    $user_vec = $matrix[$user_id];
    
    $similarities = [];
    foreach ($matrix as $other_id => $other_vec) {
        if ($other_id == $user_id) continue;
        $sim = cosineSimilarity($user_vec, $other_vec);
        if ($sim > 0) {
            $similarities[$other_id] = $sim;
        }
    }
    
    if (empty($similarities)) {
        error_log("No similar users found for user $user_id. Falling back to popular products.");
        $popular = $pdo->query("
            SELECT p.id, p.name, p.description, p.price, p.image, COUNT(o.id) as order_count
            FROM products p
            LEFT JOIN orders o ON p.id = o.product_id AND o.status != 'rejected'
            GROUP BY p.id
            ORDER BY order_count DESC, p.created_at DESC
            LIMIT $M
        ")->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($prod) {
            return array_merge($prod, ['score' => $prod['order_count']]);
        }, $popular);
    }
    
    arsort($similarities);
    $top_similar = array_slice($similarities, 0, $N, true);
    
    $product_info = $pdo->query("SELECT id, name, description, price, image FROM products")->fetchAll(PDO::FETCH_ASSOC);
    $product_map = [];
    foreach ($product_info as $prod) {
        $product_map[$prod['id']] = $prod;
    }
    
    $rec_scores = [];
    foreach ($product_map as $pid => $prod) {
        if ($user_vec[$pid] == 1) continue;
        $score = 0;
        foreach ($top_similar as $other_id => $sim) {
            $score += $sim * $matrix[$other_id][$pid];
        }
        if ($score > 0) {
            $rec_scores[$pid] = $score;
        }
    }
    
    if (empty($rec_scores)) {
        error_log("No unpurchased products with positive scores for user $user_id. Falling back to popular products.");
        $popular = $pdo->query("
            SELECT p.id, p.name, p.description, p.price, p.image, COUNT(o.id) as order_count
            FROM products p
            LEFT JOIN orders o ON p.id = o.product_id AND o.status != 'rejected'
            GROUP BY p.id
            ORDER BY order_count DESC, p.created_at DESC
            LIMIT $M
        ")->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($prod) {
            return array_merge($prod, ['score' => $prod['order_count']]);
        }, $popular);
    }
    
    arsort($rec_scores);
    $recommendations = array_slice($rec_scores, 0, $M, true);
    
    $rec_list = [];
    foreach ($recommendations as $pid => $score) {
        if (isset($product_map[$pid])) {
            $rec_list[] = array_merge($product_map[$pid], ['score' => $score]);
        }
    }
    return $rec_list;
}

// Function to get `popular combo`s
function getPopularCombos($pdo, $user_id, $matrix, $limit = 3) {
    $user_vec = $matrix[$user_id] ?? array_fill_keys(array_keys($matrix[array_key_first($matrix)] ?? []), 0);
    
    $orders = $pdo->query("SELECT buyer_id, product_id FROM orders WHERE status != 'rejected'")->fetchAll(PDO::FETCH_ASSOC);
    $cart_items = $pdo->query("SELECT buyer_id, product_id FROM cart")->fetchAll(PDO::FETCH_ASSOC);
    $interactions = array_merge($orders, $cart_items);
    
    $co_occur = [];
    foreach ($interactions as $i1) {
        foreach ($interactions as $i2) {
            if ($i1['buyer_id'] == $i2['buyer_id'] && $i1['product_id'] != $i2['product_id']) {
                $pair = [$i1['product_id'], $i2['product_id']];
                sort($pair);
                $pair_key = implode(',', $pair);
                $co_occur[$pair_key] = ($co_occur[$pair_key] ?? 0) + 1;
            }
        }
    }
    
    arsort($co_occur);
    $top_combos = array_slice($co_occur, 0, $limit, true);
    
    $product_info = $pdo->query("SELECT id, name FROM products")->fetchAll(PDO::FETCH_KEY_PAIR);
    $combo_list = [];
    foreach ($top_combos as $pair_key => $count) {
        $ids = explode(',', $pair_key);
        if (!isset($user_vec[$ids[0]]) || !isset($user_vec[$ids[1]]) || $user_vec[$ids[0]] == 0 || $user_vec[$ids[1]] == 0) {
            $combo_list[] = [
                'products' => [$product_info[$ids[0]] ?? 'Unknown', $product_info[$ids[1]] ?? 'Unknown'],
                'count' => $count
            ];
        }
    }
    return array_slice($combo_list, 0, $limit);
}

// Handle actions and invalidate cache on cart changes
$action_message = '';
if (isset($_GET['add_to_cart'])) {
    $product_id = filter_var($_GET['add_to_cart'], FILTER_VALIDATE_INT);
    if ($product_id) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO cart (buyer_id, product_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $action_message = "Item added to cart. Recommendations updated.";
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
    } else {
        $action_message = "Invalid product ID for cart.";
    }
}
if (isset($_GET['add_to_wishlist'])) {
    $product_id = filter_var($_GET['add_to_wishlist'], FILTER_VALIDATE_INT);
    if ($product_id) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO wishlist (buyer_id, product_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $action_message = "Item added to wishlist. Recommendations updated.";
    } else {
        $action_message = "Invalid product ID for wishlist.";
    }
}

// JSON output for frontend
if (isset($_GET['format']) && $_GET['format'] == 'json') {
    $matrix = buildUserItemMatrix($pdo, $cache_dir, $cache_file, $cache_time);
    $recommendations = getRecommendations($pdo, $_SESSION['user_id'], $matrix);
    $popular_combos = getPopularCombos($pdo, $_SESSION['user_id'], $matrix);
    header('Content-Type: application/json');
    echo json_encode([
        'recommendations' => $recommendations,
        'popular_combos' => $popular_combos
    ]);
    exit;
}

// Build matrix and get recommendations
$matrix = buildUserItemMatrix($pdo, $cache_dir, $cache_file, $cache_time);
$recommendations = getRecommendations($pdo, $_SESSION['user_id'], $matrix);
$popular_combos = getPopularCombos($pdo, $_SESSION['user_id'], $matrix);

// Fetch all products
$products = $pdo->query("SELECT * FROM products")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/browse.css">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Navbar -->
    <?php include '../assets/headersection/navbar.php'; ?>

    <!-- Main Content -->
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <?php if ($action_message): ?>
                <p class="text-green-600 font-bold mb-4"><?php echo htmlspecialchars($action_message); ?></p>
            <?php endif; ?>

            <!-- Debug Info -->
            <div class="mb-4 text-gray-600">
                <p>Current User ID: <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
               
                <p>Products Available: <?php echo count($products); ?></p>
            </div>

       <!-- Recommendations Section -->
            <?php if (!empty($recommendations)): ?>
                <h2 class="text-2xl font-bold mb-4">Recommended for You</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    <?php foreach ($recommendations as $rec): ?>
                        <div class="card">
                            <div class="card__shine"></div>
                            <div class="card__glow"></div>
                            <div class="card__content">
                                <div class="card__badge">Recommendation</div>
                                <div class="card__image" style="--bg-color: #a78bfa">
                                    <div class="mb-3">
                                        <img src="../assets/uploads/<?= htmlspecialchars($rec['image']) ?>" 
                                             alt="<?= htmlspecialchars($rec['name']) ?>" 
                                             class="w-full h-40 object-cover rounded" 
                                             onerror="this.src='../assets/uploads/Baklava.jpg';">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <p class="text-lg font-semibold"><?= htmlspecialchars($rec['name']) ?></p>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($rec['description']) ?></p>
                                </div>
                                <div class="flex justify-between items-center mt-3">
                                    <div class="text-green-600 font-bold">Rs. <?= number_format($rec['price'], 2) ?></div>
                                    <form method="GET" class="flex items-center gap-2">
                                        <input type="hidden" name="add_to_cart" value="<?= $rec['id'] ?>">
                                        <input type="number" name="quantity" value="1" min="1" class="w-12 text-center border rounded text-sm">
                                        <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600" title="Add to Cart">
                                            <svg height="16" width="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M4 12H20M12 4V20" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <div class="mt-4 flex justify-between text-sm">
                                    <a href="?add_to_cart=<?= $rec['id'] ?>" class="text-gray-600 hover:underline">Cart</a>
                                    <a href="checkout.php?product_id=<?= $rec['id'] ?>" class="text-blue-600 hover:underline">Buy Now</a>
                                    <a href="?add_to_wishlist=<?= $rec['id'] ?>" class="text-yellow-500 hover:underline">Wishlist</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-600 mb-10">No recommendations available yet. Browse and purchase to get personalized suggestions!</p>
            <?php endif; ?>

            <!-- Popular Combos Section -->
            <?php if (!empty($popular_combos)): ?>
                <h2 class="text-2xl font-bold mb-4">Popular Combos</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-10">
                    <?php foreach ($popular_combos as $combo): ?>
                        <div class="bg-white border rounded-lg p-4 shadow">
                            <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($combo['products'][0]) . ' + ' . htmlspecialchars($combo['products'][1]); ?></h3>
                            <p class="text-sm text-gray-600">Ordered together <?php echo $combo['count']; ?> times</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Menu Section -->
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
                                    <div class="card__image" style="--bg-color: #a78bfa">
                                        <img src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-40 object-cover mb-2 rounded" onerror="this.src='../assets/images/default.jpg';">
                                    </div>
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
                                        <a href="?add_to_cart=<?php echo $product['id']; ?>" class="text-gray-600 text-sm hover:underline">Cart</a>
                                        <a href="checkout.php?product_id=<?= $product['id'] ?>" class="text-blue-600 text-sm hover:underline">Buy Now</a>
                                        <a href="?add_to_wishlist=<?= $product['id'] ?>" class="text-yellow-500 text-sm hover:underline">Wishlist</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </main>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../assets/headersection/footer.php'; ?>
</body>
</html>