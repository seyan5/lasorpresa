<?php
require_once('conn.php'); // Includes session_start and database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate required POST data
    if (
        !isset($_POST['container_type'], $_POST['container_color'], $_POST['flower_type'], $_POST['num_flowers'], $_POST['remarks'], $_FILES['expected_image'])
        || !is_array($_POST['flower_type'])
        || !is_array($_POST['num_flowers'])
    ) {
        echo "
    <html>
    <head>
        <meta http-equiv='refresh' content='5;url=customization.php'>
        <title>Redirecting...</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background-color: #f2f2f2;
                color: #333;
            }
            .message-container {
                text-align: center;
                padding: 30px;
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 80%;
                max-width: 400px;
            }
            .message-container h2 {
                color: #e74c3c;
                font-size: 24px;
            }
            .message-container p {
                font-size: 18px;
                margin: 20px 0;
            }
            .message-container .redirect-info {
                font-size: 16px;
                color: #888;
            }
        </style>
    </head>
    <body>
        <div class='message-container'>
            <h2>Incomplete or Invalid Customization Data</h2>
            <p>Please go back and try again.</p>
            <div class='redirect-info'>
                <p>You will be redirected in <span id='countdown'>5</span> seconds.</p>
            </div>
        </div>
        <script>
            // Countdown for redirection
            var countdownElement = document.getElementById('countdown');
            var countdown = 5;
            var interval = setInterval(function() {
                countdown--;
                countdownElement.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(interval);
                }
            }, 1000);
        </script>
    </body>
    </html>
";
exit;
    }

    // Sanitize and retrieve inputs
    $container_type = htmlspecialchars($_POST['container_type']);
    $container_color = htmlspecialchars($_POST['container_color']);
    $flower_types = array_map('htmlspecialchars', $_POST['flower_type']);
    $num_flowers = array_map('intval', $_POST['num_flowers']);
    $remarks = htmlspecialchars($_POST['remarks']);

    // Ensure flower types and quantities match in count
    if (count($flower_types) !== count($num_flowers)) {
        echo "Flower type and quantity mismatch. Please try again.";
        exit;
    }

    // Handle image upload
    $expected_image = null;
    if (isset($_FILES['expected_image']) && $_FILES['expected_image']['error'] == 0) {
        $targetDir = "uploads/"; // Ensure this directory exists and is writable
        $fileName = time() . '_' . basename($_FILES['expected_image']['name']);
        $targetFilePath = $targetDir . $fileName;

        // Validate file type
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($fileType), $allowedTypes)) {
            echo "Invalid file type. Please upload an image file (jpg, jpeg, png, gif).";
            exit;
        }

        // Move the uploaded file
        if (move_uploaded_file($_FILES['expected_image']['tmp_name'], $targetFilePath)) {
            $expected_image = $fileName;
        } else {
            echo "Failed to upload the image.";
            exit;
        }
    }

    // Prepare customization details
    $customization_details = [];
    foreach ($flower_types as $index => $flower_type) {
        $customization_details[] = [
            'flower_type' => $flower_type,
            'num_flowers' => $num_flowers[$index],
            'container_type' => $container_type,
            'container_color' => $container_color,
            'remarks' => $remarks,
            'expected_image' => $expected_image // Include image in customization details
        ];
    }

    // Store customization in session
    $_SESSION['customization'] = $customization_details;

    // Redirect to customization-cart.php
    header('Location: customize-cart.php');
    exit;
} else {
    // Handle invalid request methods
    echo "Invalid request. Please use the customization form.";
    exit;
}
?>
