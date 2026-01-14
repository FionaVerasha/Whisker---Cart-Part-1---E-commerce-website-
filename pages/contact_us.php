<?php
include '../includes/header.php';
include '../includes/db.php';

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $topic = trim($_POST['topic']);
    $message = trim($_POST['message']);
    $contact_method = trim($_POST['contact_method']);

    // Handle attachment upload
    $attachment_name = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        $attachment_name = time() . "_" . basename($_FILES["attachment"]["name"]);
        $target_file = $target_dir . $attachment_name;

        if (!move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
            $error_msg = "Failed to upload attachment.";
        }
    }

    // Insert into database if no upload error
    if (!$error_msg) {
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, address, topic, message, contact_method, attachment) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $email, $address, $topic, $message, $contact_method, $attachment_name);

        if ($stmt->execute()) {
            $success_msg = "Thank you! Your message has been submitted successfully.";
        } else {
            $error_msg = "Failed to submit your message. Please try again.";
        }
    }
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">Contact Us</h2>
        <p class="mb-4 text-gray-600">We reply within 24 hours</p>

        <?php if($success_msg): ?>
            <p class="mb-4 text-green-600 font-semibold"><?php echo $success_msg; ?></p>
        <?php elseif($error_msg): ?>
            <p class="mb-4 text-red-600 font-semibold"><?php echo $error_msg; ?></p>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" class="mt-1 block w-full p-2.5 border border-gray-300 rounded-md" required>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <input type="email" id="email" name="email" class="mt-1 block w-full p-2.5 border border-gray-300 rounded-md" required>
            </div>

            <!-- Address -->
            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <input type="text" id="address" name="address" class="mt-1 block w-full p-2.5 border border-gray-300 rounded-md" required>
            </div>

            <!-- Topic -->
            <div class="mb-4">
                <label for="topic" class="block text-sm font-medium text-gray-700">Topic</label>
                <select id="topic" name="topic" class="mt-1 block w-full p-2.5 border border-gray-300 rounded-md">
                    <option value="Order issue">Order issue</option>
                    <option value="Product inquiry">Product inquiry</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- Message -->
            <div class="mb-4">
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea id="message" name="message" class="mt-1 block w-full p-2.5 border border-gray-300 rounded-md" rows="4" required></textarea>
            </div>

            <!-- Attachment -->
            <div class="mb-4">
                <label for="attachment" class="block text-sm font-medium text-gray-700">Attachment (optional)</label>
                <input type="file" id="attachment" name="attachment" class="mt-1 block w-full">
            </div>

            <!-- Contact Method -->
            <div class="mb-4 flex items-center">
                <input type="radio" id="method_attachment" name="contact_method" value="Attachment" class="mr-2" required>
                <label for="method_attachment" class="text-sm mr-4">Attachment</label>
                <input type="radio" id="method_email" name="contact_method" value="Email" class="mr-2">
                <label for="method_email" class="text-sm mr-4">Email</label>
                <input type="radio" id="method_phone" name="contact_method" value="Phone" class="mr-2">
                <label for="method_phone" class="text-sm">Phone</label>
            </div>

            <!-- Submit -->
            <div class="mb-4">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
