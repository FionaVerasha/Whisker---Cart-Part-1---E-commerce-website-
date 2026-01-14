<?php 
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include '../includes/header.php'; 
include '../includes/db.php';
?>

<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold mb-6 text-center">All Products</h2>

    <?php
    $sql = "SELECT p.id, p.product_name, p.price, p.product_image, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.id ASC"; // First added first
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="overflow-x-auto">';
        echo '<table class="min-w-full bg-white border border-gray-200">';
        echo '<thead class="bg-gray-200">';
        echo '<tr>';
        echo '<th class="px-4 py-2 border">ID</th>';
        echo '<th class="px-4 py-2 border">Image</th>';
        echo '<th class="px-4 py-2 border">Name</th>';
        echo '<th class="px-4 py-2 border">Category</th>';
        echo '<th class="px-4 py-2 border">Price</th>';
        echo '<th class="px-4 py-2 border">Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        while ($product = $result->fetch_assoc()) {
            echo '<tr class="text-center">';
            echo '<td class="px-4 py-2 border">' . $product['id'] . '</td>';
            echo '<td class="px-4 py-2 border">';
            echo '<img src="../images/' . $product['product_image'] . '" class="w-20 h-20 object-cover mx-auto">';
            echo '</td>';
            echo '<td class="px-4 py-2 border">' . $product['product_name'] . '</td>';
            echo '<td class="px-4 py-2 border">' . $product['category_name'] . '</td>';
            echo '<td class="px-4 py-2 border">Rs. ' . $product['price'] . '</td>';
            echo '<td class="px-4 py-2 border">';
            echo '<a href="adminedit_productsform.php?id=' . $product['id'] . '" class="bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600 transition">Edit</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo '<p class="text-center text-gray-500">No products available.</p>';
    }
    ?>
</div>

<?php include '../includes/footer.php'; ?>
