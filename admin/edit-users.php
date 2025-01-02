<?php
session_start();

// Include database configuration
include '../config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Fetch user details for pre-filling the form
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        die('User not found.');
    }
} else {
    die('Invalid request.');
}

// Handle Update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $user_type = $conn->real_escape_string($_POST['user_type']);

    $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, username = ?, email = ?, contact = ?, user_type = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $firstname, $lastname, $username, $email, $contact, $user_type, $id);
    if ($stmt->execute()) {
        header('Location: users.php');
        exit();
    } else {
        die('Error updating record: ' . $conn->error);
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
</head>

<body>
    <h1>Update User</h1>
    <form action="update_user.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        <input type="text" name="firstname" placeholder="First Name"
            value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
        <input type="text" name="lastname" placeholder="Last Name"
            value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
        <input type="text" name="username" placeholder="Username"
            value="<?php echo htmlspecialchars($user['username']); ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>"
            required>
        <input type="text" name="contact" placeholder="Contact"
            value="<?php echo htmlspecialchars($user['contact']); ?>" required>
        <select name="user_type">
            <option value="admin" <?php echo $user['user_type'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
            <option value="user" <?php echo $user['user_type'] === 'user' ? 'selected' : ''; ?>>User</option>
        </select>
        <button type="submit">Update User</button>
    </form>
</body>

</html>