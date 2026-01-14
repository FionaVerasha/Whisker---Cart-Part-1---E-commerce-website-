<?php
session_start();

// Admin access check
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include '../includes/header.php'; 
include '../includes/db.php';

// Handle deletion if a product ID is passed via GET
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // Fetch image filename
    $stmt = $conn->prepare("SELECT product_image FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $image_file = $product['product_image'];

        // Delete product from database
        $delete_stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $delete_stmt->bind_param("i", $delete_id);

        if ($delete_stmt->execute()) {
            // Optionally delete image from server
            if (!empty($image_file) && file_exists("../images/" . $image_file)) {
                unlink("../images/" . $image_file);
            }
            $message = "Product deleted successfully!";
        } else {
            $message = "Error deleting product: " . $delete_stmt->error;
        }
    } else {
        $message = "Product not found.";
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold mb-6 text-center">Delete Products</h2>

    <?php if(isset($message)) { ?>
        <p class="text-center text-green-600 mb-4"><?php echo $message; ?></p>
    <?php } ?>

    <?php
    // Fetch all products to display
    $sql = "SELECT p.id, p.product_name, p.price, p.product_image, c.name AS category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.id ASC";
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
            echo '<a href="admindelete_product.php?delete_id=' . $product['id'] . '" ';
            echo 'onclick="return confirm(\'Are you sure you want to delete this product?\');" ';
            echo 'class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600 transition">Delete</a>';
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
