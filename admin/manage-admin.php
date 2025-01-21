<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");

// Check if the user is an admin
if ($_SESSION['user_type'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not admin
    exit;
}

if (isset($_POST['change_user_type'])) {
    $userId = $_POST['user_id'];
    $newUserType = $_POST['user_type'];

    try {
        // Update the user type
        $stmt = $pdo->prepare("UPDATE users SET user_type = :user_type WHERE id = :id");
        $stmt->execute([':user_type' => $newUserType, ':id' => $userId]);

        echo "User type updated successfully!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

if (isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];

    try {
        // Delete the user from the database
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);

        echo "User deleted successfully!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all users from the database
try {
    $stmt = $pdo->prepare("SELECT id, name, email, user_type FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error fetching users: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../registerlogin.css">
    <title>Manage Admin</title>
</head>
<body>

    <div class="logo-container">
        <img src="../images/logo.png" alt="Logo" class="logo" />
    </div>

    <!-- Flower Image -->
    <div class="flower-container">
        <img src="../images/flower2.png" alt="Flower" class="flower" />
    </div>

    <h2>Manage Users</h2>
    <table border="1" cellspacing="0" cellpadding="5" style="width: 80%; margin: 20px auto; text-align: center;">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($user['user_type'])); ?></td>
                    <td>
                        <!-- Change User Type Button -->
                        <?php if ($user['user_type'] === 'user'): ?>
                            <form action="manage-admin.php" method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="user_type" value="admin">
                                <button type="submit" name="change_user_type">Change to Admin</button>
                            </form>
                        <?php elseif ($user['user_type'] === 'admin'): ?>
                            <form action="manage-admin.php" method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="user_type" value="user">
                                <button type="submit" name="change_user_type">Change to User</button>
                            </form>
                        <?php endif; ?>

                        <!-- Delete User Button -->
                        <form action="manage-admin.php" method="POST" style="display: inline; margin-left: 10px;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
