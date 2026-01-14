<body class="min-h-screen flex flex-col bg-gradient-to-r from-gray-600 to-slate-400">

<?php
session_start();

// Authentication check: only allow logged-in admins
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include '../includes/header.php';
?>

<div class="container mx-auto px-4 sm:px-10 py-16">
    <div class="bg-gray-300 shadow-lg rounded-lg p-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-700 mb-4 text-center sm:text-left">Product Management</h1>
        <p class="text-gray-600 mb-6 text-center sm:text-left">
            Welcome, <b><?php echo $_SESSION['username']; ?></b>! Manage products below.
        </p>

        <!-- Navigation for CRUD Features -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <a href="adminadd_products.php" 
               class="block text-center px-6 py-8 sm:px-10 sm:py-10 bg-gray-400 text-black font-bold rounded-lg text-xl sm:text-2xl hover:bg-gray-500 transition">
               ➕ Add New Product
            </a>
            <a href="adminview_product.php" 
               class="block text-center px-6 py-8 sm:px-10 sm:py-10 bg-gray-400 text-black font-bold rounded-lg text-xl sm:text-2xl hover:bg-gray-500 transition">
               📦 View Product
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="adminedit_product.php" 
               class="block text-center px-6 py-8 sm:px-10 sm:py-10 bg-gray-400 text-black font-bold rounded-lg text-xl sm:text-2xl hover:bg-gray-500 transition">
               🛒 Edit Product
            </a>
            <a href="admindelete_product.php" 
               class="block text-center px-6 py-8 sm:px-10 sm:py-10 bg-gray-400 text-black font-bold rounded-lg text-xl sm:text-2xl hover:bg-gray-500 transition">
               🗑️ Delete Product
            </a>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="mt-auto">
    <?php include '../includes/footer.php'; ?>
</footer>
</body>
