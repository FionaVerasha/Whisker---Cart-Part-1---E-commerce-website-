    <body class="min-h-screen flex flex-col bg-gradient-to-r from-gray-600 to-slate-400 rounded-b-lg">
    <?php
    session_start();
    if (isset($_SESSION['username'])) {
        header("Location:dashboard.php"); // redirect if already logged in
        exit();
    }
    include '../includes/header.php';
    ?>

    <div class="container mx-auto px-10 py-40">
        <div class="bg-gray-300 shadow-md rounded-lg p-6 max-w-md mx-auto">
            <h2 class="text-2xl font-semibold mb-4 text-center">Login</h2>
            <p class="mb-4 text-gray-600 text-center font-bold">Enter your credentials to access your account</p>

            <form action="login_process.php" method="POST" class="space-y-4">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username"
                        class="mt-1 block w-full p-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password"
                        class="mt-1 block w-full p-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                            class="w-full px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                        Login
                    </button>
                </div>
            </form>

            <!-- Extra Links -->
            <p class="text-gray-500 text-sm text-center mt-4">
                Don't have an account?
                <a href="register.php" class="text-blue-500 hover:underline">Register</a>
            </p>
        </div>
    </div>

    <footer class="mt-auto">
        <?php include '../includes/footer.php'; ?>
    </footer>
</body>