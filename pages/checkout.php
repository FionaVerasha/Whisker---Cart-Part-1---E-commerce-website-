<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// -------------------- Handle Remove / Clear Cart Actions --------------------

// Remove single item
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    header("Location: checkout.php");
    exit();
}

// Clear entire cart
if (isset($_GET['clear']) && $_GET['clear'] === '1') {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header("Location: checkout.php");
    exit();
}

// -------------------- Fetch Cart Items --------------------
$stmt = $conn->prepare("
    SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.product_name, p.price, p.product_image
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total_price += $row['subtotal'];
    $cart_items[] = $row;
}

// -------------------- Handle Place Order --------------------
$showPopup = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order']) && count($cart_items) > 0) {

    // Collect user details
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];
    $card_number = $_POST['card_number'] ?? '';
    $card_expiry = $_POST['card_expiry'] ?? '';
    $card_cvv = $_POST['card_cvv'] ?? '';

    // Insert order
    $stmtOrder = $conn->prepare("
        INSERT INTO orders 
        (user_id, total_amount, payment_method, card_number, card_expiry, card_cvv, name, email, phone, address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmtOrder->bind_param("idssssssss", $user_id, $total_price, $payment_method, $card_number, $card_expiry, $card_cvv, $name, $email, $phone, $address);
    $stmtOrder->execute();
    $order_id = $stmtOrder->insert_id;

    // Insert each cart item into order_items
    foreach ($cart_items as $item) {
        $stmtItem = $conn->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price, subtotal)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmtItem->bind_param("iiidd", $order_id, $item['product_id'], $item['quantity'], $item['price'], $item['subtotal']);
        $stmtItem->execute();
    }

    // Clear cart
    $delete = $conn->prepare("DELETE FROM cart WHERE user_id=?");
    $delete->bind_param("i", $user_id);
    $delete->execute();

    $showPopup = true;
}
?>

<body class="min-h-screen flex flex-col">
<?php include '../includes/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-center">Checkout</h1>

    <!-- Cart Items -->
    <div class="bg-gray-100 p-6 rounded shadow mb-8">
        <h2 class="text-xl font-semibold mb-4">Your Cart</h2>
        <?php if(count($cart_items) > 0): ?>
            <div class="space-y-4">
                <?php foreach($cart_items as $item): ?>
                    <div class="flex items-center gap-4 bg-white p-4 rounded shadow">
                        <img src="../images/<?php echo $item['product_image']; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="w-24 h-24 object-cover rounded">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold"><?php echo $item['product_name']; ?></h3>
                            <p>Price: Rs. <?php echo $item['price']; ?></p>
                            <p>Quantity: <?php echo $item['quantity']; ?></p>
                            <p>Subtotal: Rs. <?php echo $item['subtotal']; ?></p>
                        </div>
                        <div>
                            <!-- Remove button -->
                            <a href="checkout.php?remove=<?php echo $item['cart_id']; ?>" 
                               class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600">Remove</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Clear Cart & Total -->
            <div class="flex justify-between items-center mt-4">
                <a href="checkout.php?clear=1" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">
                    Clear Cart
                </a>
                <div class="text-right font-bold text-xl">Total: Rs. <?php echo $total_price; ?></div>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500">Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <!-- User Details & Payment -->
    <?php if(count($cart_items) > 0): ?>
    <form method="POST" class="bg-gray-100 p-6 rounded shadow space-y-4">
        <h2 class="text-xl font-semibold mb-4">Your Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" name="name" placeholder="Full Name" required class="p-2 border rounded w-full">
            <input type="email" name="email" placeholder="Email" required class="p-2 border rounded w-full">
            <input type="text" name="phone" placeholder="Phone Number" required class="p-2 border rounded w-full">
            <input type="text" name="address" placeholder="Address" required class="p-2 border rounded w-full">
        </div>

        <!-- Payment Method -->
        <div>
            <h3 class="font-semibold mb-2">Payment Method</h3>
            <select name="payment_method" id="payment_method" required class="p-2 border rounded w-full">
                <option value="">Select Payment Method</option>
                <option value="cod">Cash on Delivery</option>
                <option value="card">Card Payment</option>
            </select>
        </div>

        <!-- Card Details -->
        <div id="cardDetails" class="hidden space-y-2">
            <input type="text" name="card_number" placeholder="Card Number" class="p-2 border rounded w-full">
            <input type="text" name="card_expiry" placeholder="Expiry (MM/YY)" class="p-2 border rounded w-full">
            <input type="text" name="card_cvv" placeholder="CVV" class="p-2 border rounded w-full">
        </div>

        <button type="submit" name="place_order" class="bg-green-500 text-white py-2 px-6 rounded hover:bg-green-600 transition">
            Place Order
        </button>
    </form>
    <?php endif; ?>
</div>

<!-- Popup -->
<?php if($showPopup): ?>
<div id="orderPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow text-center">
        <p class="text-xl font-bold mb-4">Thank you for choosing Whisker Cart!</p>
        <a href="index.php" class="bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600">Continue Shopping</a>
    </div>
</div>
<?php endif; ?>

<!-- Footer -->
<footer class="mt-auto">
    <?php include '../includes/footer.php'; ?>
</footer>

<script>
// Show/hide card details based on payment method
document.getElementById('payment_method')?.addEventListener('change', function() {
    if (this.value === 'card') {
        document.getElementById('cardDetails').classList.remove('hidden');
    } else {
        document.getElementById('cardDetails').classList.add('hidden');
    }
});
</script>
