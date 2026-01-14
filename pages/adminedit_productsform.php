<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include '../includes/db.php';
include '../includes/header.php'; 

if (!isset($_GET['id'])) {
    echo "No product selected.";
    exit();
}

$product_id = intval($_GET['id']);

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Product not found.";
    exit();
}

$product = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name']);
    $price = trim($_POST['price']);
    $category_id = $_POST['category'];
    $description = trim($_POST['description']);

    // Handle image
    if (!empty($_FILES['product_image']['name'])) {
        $image = time() . "_" . basename($_FILES['product_image']['name']);
        $target_file = "../images/" . $image;
        if (getimagesize($_FILES['product_image']['tmp_name'])) {
            move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file);
        } else {
            echo "Invalid image file.";
            exit();
        }
    } else {
        $image = $product['product_image'];
    }

    $update_stmt = $conn->prepare("UPDATE products SET product_name=?, price=?, category_id=?, description=?, product_image=? WHERE id=?");
    $update_stmt->bind_param("sdsssi", $product_name, $price, $category_id, $description, $image, $product_id);

    if ($update_stmt->execute()) {
        header("Location: adminedit_product.php?message=Product updated successfully!");
        exit();
    } else {
        echo "Error updating product: " . $update_stmt->error;
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold mb-6 text-center">Edit Product</h2>

    <form action="" method="POST" enctype="multipart/form-data" class="max-w-lg mx-auto bg-gray-100 p-6 rounded shadow">
        <!-- Product Name -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Product Name</label>
            <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" class="w-full p-2 border rounded" required>
        </div>

        <!-- Price -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Price</label>
            <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" class="w-full p-2 border rounded" required>
        </div>

        <!-- Category -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Category</label>
            <select name="category" class="w-full p-2 border rounded" required>
                <?php
                $cat_result = $conn->query("SELECT * FROM categories");
                while ($cat = $cat_result->fetch_assoc()) {
                    $selected = ($cat['id'] == $product['category_id']) ? "selected" : "";
                    echo "<option value='" . $cat['id'] . "' $selected>" . $cat['name'] . "</option>";
                }
                ?>
            </select>
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Description</label>
            <textarea name="description" rows="4" class="w-full p-2 border rounded" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <!-- Current Image -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Current Image</label>
            <img src="../images/<?php echo $product['product_image']; ?>" class="w-32 h-32 object-cover mb-2">
        </div>

        <!-- Upload New Image -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Replace Image (optional)</label>
            <input type="file" name="product_image" class="w-full p-2 border rounded">
        </div>

        <!-- Submit -->
        <div class="mb-4 text-center">
            <button type="submit" class="bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600 transition">Save Changes</button>
        </div>
    </form>
</div>


<?php include '../includes/footer.php'; ?>
