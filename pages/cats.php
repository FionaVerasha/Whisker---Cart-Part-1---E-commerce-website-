<!-- Header -->
<body class="min-h-screen flex flex-col">
<?php include '../includes/header.php'; ?>

<h1 class="text-4xl font-bold underline text-gray-800 rounded-lg p-10 text-center">CATS</h1>

<?php
include '../includes/db.php'; // Include the database connection

// Step 1: Fetch products in the "Cats" category
$category_id = 2; // Assuming "Cats" has category_id = 2

$sql = "SELECT * FROM products WHERE category_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

// Step 3: Display products in a responsive grid
if ($result->num_rows > 0) {
    echo '<div class="flex flex-wrap gap-6 justify-center">';

    while ($product = $result->fetch_assoc()) {
        echo '<div class="max-w-sm rounded overflow-hidden shadow-lg bg-gray-200 p-4">';
        
        // Product image
        echo '<div class="w-full h-64 bg-cover bg-center" style="background-image: url(../images/' . $product['product_image'] . ');"></div>';
        
        echo '<div class="p-4">';
        
        // Product name
        echo '<h3 class="text-xl font-semibold text-center mb-2">' . $product['product_name'] . '</h3>';
        
        // Product price
        echo '<p class="text-lg text-gray-500 text-center mb-2">Rs. ' . $product['price'] . '</p>';
        
        // Product description (truncated)
        echo '<p class="text-sm text-gray-600 mb-4 line-clamp-2">' . substr($product['description'], 0, 100) . '...</p>';
        
        // View product details button
        echo '<div class="flex justify-center">';
        echo '<a href="view_productdetails.php?id=' . $product['id'] . '" class="bg-gray-700 text-white py-2 px-4 rounded hover:bg-gray-900 transition duration-300">View Product Details</a>';
        echo '</div>';
        
        echo '</div>'; // End of product card inner content
        echo '</div>'; // End of product card
    }

    echo '</div>'; // End of product grid
} else {
    echo "<p class='text-center text-gray-500'>No products available in the Cats category.</p>";
}
?>

<!-- Footer -->
<footer class="mt-auto pt-10">
    <?php include '../includes/footer.php'; ?>
</footer>
</body>
