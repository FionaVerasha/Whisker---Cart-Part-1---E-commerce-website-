<?php
session_start();
include '../includes/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = "view_productdetails.php?id=" . ($_GET['id'] ?? '');
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_GET['id'] ?? 0);
if (!$product_id) exit("Product not specified.");

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) exit("Product not found.");

$product = $result->fetch_assoc();

// Handle Add to Cart submission
$showPopup = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) {
    $quantity = max(1, intval($_POST['quantity']));

    // Insert or update cart
    $stmtCart = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity)
                                VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
    $stmtCart->bind_param("iii", $user_id, $product_id, $quantity);
    $stmtCart->execute();

    $showPopup = true;
}

// Fetch updated cart count
$cart_count = 0;
$stmt2 = $conn->prepare("SELECT SUM(quantity) AS total_items FROM cart WHERE user_id=?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
$row2 = $res2->fetch_assoc();
$cart_count = $row2['total_items'] ?? 0;
?>

<?php include '../includes/header.php'; ?>

<div class="container mx-auto px-4 py-8 mt-10 flex flex-col md:flex-row gap-8">

    <!-- Left Column: Product Image -->
    <div class="md:w-1/2">
        <img src="../images/<?php echo $product['product_image']; ?>" 
             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
             class="w-full h-auto object-cover rounded shadow">
    </div>

    <!-- Right Column: Product Info -->
    <div class="md:w-1/2 flex flex-col gap-4">
        <h2 class="text-3xl font-bold"><?php echo $product['product_name']; ?></h2>
        <p class="text-2xl text-green-600">Rs. <?php echo $product['price']; ?></p>
        <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

        <!-- Quantity & Add to Cart Form -->
        <form method="POST" class="flex flex-col gap-3 mt-4">
            <label for="quantity" class="font-semibold">Quantity:</label>
            <input type="number" name="quantity" id="quantity" value="1" min="1" class="w-24 p-2 border rounded">
            <button type="submit" class="bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600 transition w-max">
                Add to Cart
            </button>
        </form>
    </div>
</div>

<!-- Popup after adding to cart -->
<?php if($showPopup) { ?>
<div id="cartPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow text-center">
        <p class="mb-4 font-semibold">Product added to cart!</p>
        <div class="flex gap-4 justify-center">
            <button id="continueShopping" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">Continue Shopping</button>
            <a href="checkout.php" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">Proceed to Checkout</a>
        </div>
    </div>
</div>
<?php } ?>

<!-- Footer -->
<footer class="mt-auto">
    <?php include '../includes/footer.php'; ?>
</footer>

<script>
<?php if($showPopup) { ?>
document.getElementById('continueShopping').addEventListener('click', function() {
    document.getElementById('cartPopup').remove();
});
<?php } ?>
</script>
