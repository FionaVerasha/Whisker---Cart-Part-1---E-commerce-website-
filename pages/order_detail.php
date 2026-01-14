<?php
session_start();
include '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get order ID from URL
$order_id = intval($_GET['id'] ?? 0);
if (!$order_id) {
    exit("Order not specified.");
}

// Verify that the order belongs to the logged-in user
$stmt = $conn->prepare("SELECT id, total_amount, payment_method, created_at FROM orders WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    exit("Order not found or access denied.");
}

$order = $result->fetch_assoc();

// Fetch order items
$stmtItems = $conn->prepare("
    SELECT p.product_name, p.product_image, oi.quantity, oi.price, oi.subtotal
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id=?
");
$stmtItems->bind_param("i", $order_id);
$stmtItems->execute();
$resultItems = $stmtItems->get_result();
$order_items = $resultItems->fetch_all(MYSQLI_ASSOC);
?>

<body class="min-h-screen flex flex-col bg-gradient-to-r from-gray-600 to-slate-400 rounded-b-lg">
<?php include '../includes/header.php'; ?>

<div class="container mx-auto px-60 py-20">
    <div class="bg-gray-300 shadow-lg rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-700 mb-4">Order Details</h1>
        <p class="text-gray-600 mb-6">
            <b>Order ID:</b> <?php echo $order['id']; ?><br>
            <b>Date:</b> <?php echo $order['created_at']; ?><br>
            <b>Payment Method:</b> <?php echo ucfirst($order['payment_method']); ?>
        </p>

        <?php if(count($order_items) > 0): ?>
            <div class="space-y-4">
                <?php foreach($order_items as $item): ?>
                    <div class="flex items-center gap-4 bg-white p-4 rounded shadow">
                        <img src="../images/<?php echo $item['product_image']; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="w-24 h-24 object-cover rounded">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold"><?php echo $item['product_name']; ?></h3>
                            <p>Price: Rs. <?php echo $item['price']; ?></p>
                            <p>Quantity: <?php echo $item['quantity']; ?></p>
                            <p>Subtotal: Rs. <?php echo $item['subtotal']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-right mt-4 font-bold text-xl">
                Total Amount: Rs. <?php echo $order['total_amount']; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500">No items found for this order.</p>
        <?php endif; ?>

        <div class="mt-6">
            <a href="customer_dashboard.php" class="bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600">Back to Dashboard</a>
        </div>
    </div>
</div>

<footer class="mt-auto">
    <?php include '../includes/footer.php'; ?>
</footer>
</body>
