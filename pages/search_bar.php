<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

$search_query = trim($_GET['query'] ?? '');
?>

<body class="min-h-screen flex flex-col">

<div class="container mx-auto px-10 py-10">
    <h1 class="text-3xl font-bold mb-6 text-center">Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>

    <?php
    if ($search_query !== '') {
        // Search products by name or description
        $stmt = $conn->prepare("
            SELECT * FROM products 
            WHERE product_name LIKE ? OR description LIKE ?
            ORDER BY id DESC
        ");
        $like_query = "%" . $search_query . "%";
        $stmt->bind_param("ss", $like_query, $like_query);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';
            while ($product = $result->fetch_assoc()) {
                echo '<div class="max-w-sm rounded overflow-hidden shadow-lg bg-gray-200 p-4 flex flex-col">';
                echo '<div class="w-full h-64 bg-cover bg-center rounded mb-4" style="background-image: url(../images/' . $product['product_image'] . ');"></div>';
                echo '<h3 class="text-xl font-semibold text-center mb-2">' . htmlspecialchars($product['product_name']) . '</h3>';
                echo '<p class="text-lg text-gray-500 text-center mb-2">Rs. ' . $product['price'] . '</p>';
                echo '<p class="text-sm text-gray-600 mb-4 line-clamp-2">' . substr($product['description'], 0, 100) . '...</p>';
                echo '<div class="flex justify-center mt-auto">';
                echo '<a href="view_productdetails.php?id=' . $product['id'] . '" class="bg-gray-700 text-white py-2 px-4 rounded hover:bg-gray-900 transition duration-300">View Product Details</a>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo "<p class='text-center text-gray-500'>No products found matching your search.</p>";
        }
    } else {
        echo "<p class='text-center text-gray-500'>Please enter a search term.</p>";
    }
    ?>
</div>

<!-- Footer -->
<footer class="mt-auto pt-10">
    <?php include '../includes/footer.php'; ?>
</footer>
</body>
