<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Fix</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f8f8f8;
            padding: 15px 30px;
            border-bottom: 1px solid #ddd;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logos {
            width: 10rem; /* Logo size adjustment */
        }

        .navbar {
            display: flex;
            gap: 20px; /* Spacing between navbar items */
        }

        .navbar a {
            font-size: 1.2rem;
            color: #666;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .navbar a:hover {
            background-color: #007bff; /* Highlight background color on hover */
            color: #fff; /* Text color on hover */
        }

        .navbar a.active {
            background-color: #0056b3; /* Active link color */
            color: #fff;
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar {
                flex-wrap: wrap;
                gap: 10px;
            }

            .navbar a {
                font-size: 1rem;
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body>
<header>
    <img src="../images/logo.png" alt="Logo" class="logos">
    <nav class="navbar">
        <a href="index.php">Home</a>
        <a href="customer-profile-update.php">Update Profile</a>
        <a href="customer-shipping-info.php">Update Shipping Info</a>
        <a href="customer-password-update.php">Update Password</a>
        <a href="customer-order.php">Orders</a>
        <a href="customize-view.php">Custom Orders</a>
    </nav>
</header>
</body>
</html>
