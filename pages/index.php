<?php include '../includes/header.php'; ?>
<?php include '../includes/db.php'; ?>

<?php
// Function to fetch products by category name
function getProductsByCategory($conn, $category_name, $limit = 4) {
    $stmt = $conn->prepare("
        SELECT * FROM products 
        WHERE category_id = (SELECT id FROM categories WHERE name = ?)
        ORDER BY id ASC
        LIMIT ?
    ");
    $stmt->bind_param("si", $category_name, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch dynamic product sections
$best_sellers = getProductsByCategory($conn, 'best_sellers', 3);
$featured = getProductsByCategory($conn, 'featured_products', 4);
$new_arrivals = getProductsByCategory($conn, 'new_arrivals', 4);
?>

<!-- Hero Banner -->
<section class="relative">
    <img src='../images/main.webp' alt="Hero Banner" class="w-full h-80 object-cover a-top">
    <div class="absolute inset-0 bg-black opacity-60"></div>
    <div class="absolute inset-0 flex items-center justify-center text-center text-white">
        <div class="flex flex-col space-y-4">
            <h1 class="text-4xl font-semibold">Find the Best Pet Products</h1>
            <p class="text-lg">Shop for your furry friend with ease</p>
            <a href="all_products.php" class="inline-block bg-blue-800 text-white py-2 px-6 rounded-lg">Shop Now</a>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="py-12 bg-gray-400">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Featured Categories</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 px-8 lg:px-16"> 
            <div class="bg-white p-6 rounded-lg shadow-lg mb-4">
                <img src="../images/dog.jpg" alt="Dog Products" class="w-full h-48 object-cover rounded-md mb-4">
                <h3 class="text-xl font-semibold p-3">Dogs</h3>
                <a href="dogs.php?category=dog" class="mt-4 bg-gray-700 text-white py-2 px-4 rounded-lg">Explore</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg mb-4">
                <img src="../images/cats.jpg" alt="Cat Products" class="w-full h-48 object-cover rounded-md mb-4">
                <h3 class="text-xl font-semibold p-3">Cats</h3>
                <a href="cats.php?category=cat" class="mt-4 bg-gray-700 text-white py-2 px-4 rounded-lg">Explore</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg mb-4">
                <img src="../images/accessories.jpg" alt="Accessories" class="w-full h-48 object-cover rounded-md mb-4">
                <h3 class="text-xl font-semibold p-3">Accessories</h3>
                <a href="accessories.php?category=accessories" class="mt-4 bg-gray-700 text-white py-2 px-4 rounded-lg">Explore</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg mb-4">
                <img src="../images/grooming.jpg" alt="Grooming Products" class="w-full h-48 object-cover rounded-md mb-4">
                <h3 class="text-xl font-semibold p-3">Grooming</h3>
                <a href="grooming.php?category=grooming" class=" mt-4 bg-gray-700 text-white py-2 px-4 rounded-lg">Explore</a>
            </div>
        </div>
    </div>
</section>

<!-- Best Sellers -->
<section class="p-6 py-12 bg-gray-200">
    <div class="container mx-auto text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Best Sellers</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($best_sellers as $product): ?>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <img src="../images/<?php echo $product['product_image']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="w-full h-61 object-cover rounded-md mb-4">
                <h3 class="text-xl font-semibold"><?php echo $product['product_name']; ?></h3>
                <p class="text-gray-500 p-2">Rs. <?php echo $product['price']; ?></p>
                <a href="view_productdetails.php?id=<?php echo $product['id']; ?>" class="mt-4 bg-gray-700 text-white py-2 px-4 rounded-lg">View Details</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="bg-gray-800 text-white py-12 text-center">
    <h2 class="text-3xl font-bold mb-4">Get 20% OFF on Your First Order</h2>
    <p class="mb-6">Use promo code FIRST20 during checkout</p>
    <a href="all_products.php" class="bg-yellow-400 text-gray-800 py-2 px-6 rounded-lg">Shop Now</a>
</section>



<!-- Featured Products -->
<section class="py-20 bg-gray-300">
    <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">Featured Products</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 px-4">
        <?php foreach($featured as $product): ?>
        <div class="flex flex-col items-center bg-white p-6 rounded-lg shadow-md">
            <img src="../images/<?php echo $product['product_image']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="w-64 h-64 object-cover mb-4">
            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo $product['product_name']; ?></h3>
            <p class="text-gray-600">Rs. <?php echo $product['price']; ?></p>
            <a href="view_productdetails.php?id=<?php echo $product['id']; ?>" class="mt-4 bg-gray-700 text-white py-2 px-4 rounded-lg">View Product</a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- New Arrivals -->
<section class="py-12 bg-gray-500">
    <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">New Arrivals</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 px-4">
        <?php foreach($new_arrivals as $product): ?>
        <div class="flex flex-col items-center bg-white p-6 rounded-lg shadow-md">
            <img src="../images/<?php echo $product['product_image']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="w-64 h-64 object-cover mb-4">
            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo $product['product_name']; ?></h3>
            <p class="text-gray-600">Rs. <?php echo $product['price']; ?></p>
            <a href="view_productdetails.php?id=<?php echo $product['id']; ?>" class="mt-4 bg-gray-700 text-white py-2 px-4 rounded-lg">View Product</a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Footer -->
<?php include '../includes/footer.php'; ?>
