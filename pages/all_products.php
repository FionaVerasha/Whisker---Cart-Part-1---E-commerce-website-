<?php
session_start();
include '../includes/header.php';
include '../includes/db.php'; // Database connection

?>

<body class="min-h-screen flex flex-col">

<h1 class="text-4xl font-bold underline text-gray-800 rounded-lg p-10 text-center">All Products</h1>

<?php
// Fetch all products from the database
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-10 mb-10">';

    while ($product = $result->fetch_assoc()) {
        echo '<div class="max-w-sm rounded overflow-hidden shadow-lg bg-gray-200 p-4 flex flex-col">';
        
        // Product image
        echo '<div class="w-full h-64 bg-cover bg-center rounded mb-4" style="background-image: url(../images/' . $product['product_image'] . ');"></div>';
        
        // Product info
        echo '<div class="flex flex-col flex-1">';
        echo '<h3 class="text-xl font-semibold text-center mb-2">' . htmlspecialchars($product['product_name']) . '</h3>';
        echo '<p class="text-lg text-gray-500 text-center mb-2">Rs. ' . $product['price'] . '</p>';
        echo '<p class="text-sm text-gray-600 mb-4 line-clamp-2">' . substr($product['description'], 0, 100) . '...</p>';
        
        // View Product Details button
        echo '<div class="flex justify-center mt-auto">';
        echo '<a href="view_productdetails.php?id=' . $product['id'] . '" class="bg-gray-700 text-white py-2 px-4 rounded hover:bg-gray-900 transition duration-300">View Product Details</a>';
        echo '</div>';
        echo '</div>'; // End product info
        
        echo '</div>'; // End product card
    }

    echo '</div>'; // End of grid
} else {
    echo "<p class='text-center text-gray-500'>No products available.</p>";
}
?>

<!-- Footer -->
<footer class="mt-auto pt-10">
    <?php include '../includes/footer.php'; ?>
</footer>
</body>
