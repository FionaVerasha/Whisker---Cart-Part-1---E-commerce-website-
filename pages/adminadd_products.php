<body class="min-h-screen flex flex-col bg-gradient-to-r from-gray-600 to-slate-400 rounded-b-lg">
<?php
session_start();

// Authentication check: only allow logged-in admins
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // redirect non-admins to login
    exit();
}

include '../includes/header.php';
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $product_name = trim($_POST['product_name']);
    $price = trim($_POST['price']);
    $category_id = $_POST['category']; // Get the selected category ID
    $description = trim($_POST['description']);
    
    // Handle image upload
    $target_dir = "../images/";
    $image = time() . "_" . basename($_FILES["product_image"]["name"]);
    $target_file = $target_dir . $image;

    // Allowed file types and size check
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = $_FILES["product_image"]["type"];
    $file_size = $_FILES["product_image"]["size"];
    $max_size = 5000000; // Max file size: 5MB

    if (!in_array($file_type, $allowed_types)) {
        echo "Invalid image type. Only JPG, PNG, and GIF are allowed.";
        exit();
    }

    if ($file_size > $max_size) {
        echo "File is too large. Maximum size is 5MB.";
        exit();
    }

    // Check if the file is a valid image
    if (getimagesize($_FILES["product_image"]["tmp_name"]) !== false) {
        // Move uploaded file to the images folder
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            // Insert new product into the database
            $stmt = $conn->prepare("INSERT INTO products (product_name, price, category_id, description, product_image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sdiss", $product_name, $price, $category_id, $description, $image);
            
            if ($stmt->execute()) {
                header("Location: admin_dashboard.php?message=Product added successfully!");
                exit();
            } else {
                echo "Error: " . $stmt->error; // Provide more detailed error for debugging
            }
        } else {
            echo "Error uploading image.";
        }
    } else {
        echo "Invalid image file.";
    }
}
?>

<div class="container mx-auto px-10 py-40">
    <div class="bg-gray-300 shadow-lg rounded-lg p-6 max-w-md mx-auto">
        <h2 class="text-2xl font-semibold mb-4 text-center">Add New Product</h2>
        
        <form action="adminadd_products.php" method="POST" enctype="multipart/form-data">
            <!-- Product Name -->
            <div class="mb-4">
                <label for="product_name" class="block text-sm font-medium text-gray-700">Product Name</label>
                <input type="text" id="product_name" name="product_name" class="mt-1 block w-full p-2.5 border border-gray-300 rounded-md" required>
            </div>

            <!-- Product Price -->
            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                <input type="number" step="0.01" id="price" name="price" class="mt-1 block w-full p-2.5 border border-gray-300 rounded-md" required>
            </div>

            <!-- Category Dropdown -->
            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select id="category" name="category" class="mt-1 block w-full p-2.5 border border-gray-300 rounded-md" required>
                    <?php
                        // Fetch categories from the database
                        $sql = "SELECT * FROM categories";
                        $result = $conn->query($sql);
                        
                        // Display each category in the dropdown
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                            }
                        } else {
                            echo "<option>No categories available</option>";
                        }
                    ?>
                </select>
            </div>

            <!-- Product Description -->
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" name="description" class="mt-1 block w-full p-2.5 border border-gray-300 rounded-md" rows="4" required></textarea>
            </div>

            <!-- Product Image -->
            <div class="mb-4">
                <label for="product_image" class="block text-sm font-medium text-gray-700">Product Image</label>
                <input type="file" id="product_image" name="product_image" class="mt-1 block w-full p-2.5 border border-gray-300 rounded-md" required>
            </div>

            <!-- Submit Button -->
            <div class="mb-4">
                <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Add Product</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>


