<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $cart_count = $row['total_items'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Whisker Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<!-- Navigation -->
<nav class="bg-gray-800 p-4 relative">
    <div class="flex justify-between items-center">
        <!-- Logo -->
        <div class="flex items-center space-x-3">
            <a href="index.php">
                <img src="../images/logo.png" alt="Logo" class="h-16 w-auto">
            </a>
            <a href="index.php" class="text-white text-xl font-bold">WhiskerCart</a>
        </div>

        <!-- Desktop Menu -->
        <div class="hidden md:flex space-x-6 items-center text-white">
            <a href="index.php" class="hover:text-gray-400 mr-5">Home</a>

            <!-- Categories Dropdown -->
            <div class="relative group ">
                <div class="cursor-pointer hover:text-gray-400 mr-5">Categories</div>
                <div class="absolute left-0 hidden bg-gray-700 text-white rounded-md shadow-lg group-hover:block z-10">
                    <a href="all_products.php" class="block px-4 py-2 text-sm">All Products</a>
                    <a href="dogs.php" class="block px-4 py-2 text-sm">Dogs</a>
                    <a href="cats.php" class="block px-4 py-2 text-sm">Cats</a>
                </div>

            
            </div>
            <div class="relative">
                <a href="accessories.php" class="hover:text-gray-400 mr-10">Accessories</a>
                <a href="grooming.php" class="hover:text-gray-400 mr-10">Grooming</a>
                <a href="about_us.php" class="hover:text-gray-400 mr-10">About Us</a>
            </div>


            <!-- Search Bar -->
            <form action="search_bar.php" method="GET" class="flex mt-3">
                <input type="text" name="query" placeholder="Search..." 
                       class="p-2 rounded-l-md border-3 border-gray-400 w-48 text-black" required>
                <button type="submit" class="bg-blue-600 text-black px-3 rounded-r-md hover:bg-blue-700">
                    <i class="fa fa-search"></i>
                </button>
            </form>

            <!-- Cart & Account -->
            <div class="flex items-center space-x-4 ml-10">
                <!-- Cart -->
                <a href="checkout.php" class="relative text-white">
                    <i class="fa fa-shopping-cart"></i> Cart
                    <?php if($cart_count > 0): ?>
                        <span class="absolute -top-2 -right-3 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full">
                            <?php echo $cart_count; ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Account Dropdown -->
                <div class="relative group">
                    <div class="flex items-center cursor-pointer">
                        <i class="fa fa-user mr-1"></i> Account
                    </div>
                    <div class="absolute right-0 hidden bg-gray-700 text-white rounded-md shadow-lg group-hover:block z-10">
                        <a href="login.php" class="block px-4 py-2 text-sm hover:bg-gray-600">Login</a>
                        <a href="logout.php" class="block px-4 py-2 text-sm hover:bg-gray-600">Logout</a>
                        <a href="register.php" class="block px-4 py-2 text-sm hover:bg-gray-600">Register</a>
                        <a href="dashboard.php" class="block px-4 py-2 text-sm hover:bg-gray-600">Dashboard</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hamburger Button (Mobile) -->
        <div class="md:hidden">
            <button id="mobile-menu-button" class="text-white focus:outline-none">
                <i class="fa fa-bars fa-2x"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-gray-700 w-full absolute left-0 mt-2 rounded-b-lg z-10 flex flex-col">
        <a href="index.php" class="block px-6 py-3 text-white hover:bg-gray-600">Home</a>
        <a href="all_products.php" class="block px-6 py-3 text-white hover:bg-gray-600">All Products</a>
        <a href="dogs.php" class="block px-6 py-3 text-white hover:bg-gray-600">Dogs</a>
        <a href="cats.php" class="block px-6 py-3 text-white hover:bg-gray-600">Cats</a>
        <a href="accessories.php" class="block px-6 py-3 text-white hover:bg-gray-600">Accessories</a>
        <a href="grooming.php" class="block px-6 py-3 text-white hover:bg-gray-600">Grooming</a>
        <a href="about_us.php" class="block px-6 py-3 text-white hover:bg-gray-600">About Us</a>

        <!-- Mobile Search Bar -->
        <form action="search_bar.php" method="GET" class="flex px-6 py-3">
            <input type="text" name="query" placeholder="Search..." 
                   class="p-2 rounded-l-md border-2 border-gray-400 w-full" required>
            <button type="submit" class="bg-blue-600 text-white px-3 rounded-r-md hover:bg-blue-700">
                <i class="fa fa-search"></i>
            </button>
        </form>

        <!-- Mobile Cart & Account -->
        <a href="checkout.php" class="block px-6 py-3 text-white hover:bg-gray-600 relative">
            <i class="fa fa-shopping-cart"></i> Cart
            <?php if($cart_count > 0): ?>
                <span class="absolute top-3 right-6 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full">
                    <?php echo $cart_count; ?>
                </span>
            <?php endif; ?>
        </a>
        <a href="dashboard.php" class="block px-6 py-3 text-white hover:bg-gray-600">Dashboard</a>
        <a href="login.php" class="block px-6 py-3 text-white hover:bg-gray-600">Login</a>
        <a href="register.php" class="block px-6 py-3 text-white hover:bg-gray-600">Register</a>
        <a href="logout.php" class="block px-6 py-3 text-white hover:bg-gray-600">Logout</a>
    </div>
</nav>

<script>
const btn = document.getElementById('mobile-menu-button');
const menu = document.getElementById('mobile-menu');

btn.addEventListener('click', () => {
    menu.classList.toggle('hidden');
});
</script>
