<?php
session_start();

// Authentication check: only allow logged-in customers
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'customer') {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';
include '../includes/header.php';

// Fetch user info safely
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if (!$user) {
        // Default values if user not found
        $user = ['username' => '', 'email' => ''];
    }
} else {
    die("Failed to fetch user details.");
}

// Fetch user's orders
$stmtOrders = $conn->prepare("
    SELECT id, total_amount, payment_method, created_at
    FROM orders
    WHERE user_id=?
    ORDER BY created_at DESC
");
$stmtOrders->bind_param("i", $user_id);
$stmtOrders->execute();
$resultOrders = $stmtOrders->get_result();
$orders = $resultOrders->fetch_all(MYSQLI_ASSOC);

// Handle account update
$account_update_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_account'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmtUpdate = $conn->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
        $stmtUpdate->bind_param("sssi", $username, $email, $hashed_password, $user_id);
    } else {
        $stmtUpdate = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
        $stmtUpdate->bind_param("ssi", $username, $email, $user_id);
    }

    if ($stmtUpdate->execute()) {
        $account_update_msg = "Account updated successfully!";
        $_SESSION['username'] = $username;
        $user['username'] = $username;
        $user['email'] = $email;
    } else {
        $account_update_msg = "Failed to update account.";
    }
}
?>

<body class="min-h-screen flex flex-col bg-gradient-to-r from-gray-600 to-slate-400">
<div class="container mx-auto px-4 sm:px-10 py-10">
    <div class="bg-gray-300 shadow-lg rounded-lg p-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-700 mb-4 text-center sm:text-left">Customer Dashboard</h1>
        <p class="text-gray-600 mb-6 text-center sm:text-left">
            Hi, <b><?php echo htmlspecialchars($user['username']); ?></b>! Manage your account and orders below.
        </p>

        <!-- Account Management -->
        <div class="mb-6 bg-gray-200 p-4 sm:p-6 rounded shadow">
            <h2 class="text-xl sm:text-2xl font-bold mb-4">Account Details</h2>
            <?php if($account_update_msg): ?>
                <p class="text-green-600 font-semibold mb-2"><?php echo $account_update_msg; ?></p>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block font-semibold">Username:</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="p-2 border rounded w-full" required>
                </div>
                <div>
                    <label class="block font-semibold">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="p-2 border rounded w-full" required>
                </div>
                <div>
                    <label class="block font-semibold">Password (leave blank to keep unchanged):</label>
                    <input type="password" name="password" class="p-2 border rounded w-full">
                </div>
                <button type="submit" name="update_account" class="bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600 w-full sm:w-auto">Update Account</button>
            </form>
        </div>

        <!-- Orders Management -->
        <div class="mb-6 bg-gray-200 p-4 sm:p-6 rounded shadow">
            <h2 class="text-xl sm:text-2xl font-bold mb-4">Your Orders</h2>
            <?php if(count($orders) > 0): ?>
                <div class="space-y-4">
                    <?php foreach($orders as $order): ?>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-4 rounded shadow">
                            <div class="mb-2 sm:mb-0">
                                <p><b>Order ID:</b> <?php echo $order['id']; ?></p>
                                <p><b>Total:</b> Rs. <?php echo $order['total_amount']; ?></p>
                                <p><b>Payment:</b> <?php echo ucfirst($order['payment_method']); ?></p>
                                <p><b>Date:</b> <?php echo $order['created_at']; ?></p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="bg-gray-700 text-white py-2 px-4 rounded hover:bg-gray-800 mt-2 sm:mt-0 block sm:inline-block">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500">You have no orders yet.</p>
            <?php endif; ?>
        </div>

        <!-- Manage Cart -->
        <div class="bg-gray-200 p-4 sm:p-6 rounded shadow">
            <h2 class="text-xl sm:text-2xl font-bold mb-4">Manage Cart</h2>
            <a href="checkout.php" class="bg-green-500 text-white py-2 px-6 rounded hover:bg-green-600 block w-full sm:inline-block text-center">View/Edit Cart</a>
        </div>
    </div>
</div>

<footer class="mt-auto">
    <?php include '../includes/footer.php'; ?>
</footer>
</body>
