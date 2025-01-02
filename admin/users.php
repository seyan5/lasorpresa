<?php
// users.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lasorpresa";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php");
    exit;
}

// Handle edit request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $id = intval($_POST['id']);
    $username = $_POST['username'];
    $email = $_POST['email'];

    $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $email, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php");
    exit;
}

// Fetch users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 3px;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-edit {
            background-color: #ffc107;
        }
        .form-container {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Manage Users</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td>
                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    <button onclick="openEditForm(<?php echo $row['id']; ?>, '<?php echo $row['username']; ?>', '<?php echo $row['email']; ?>')" class="btn btn-edit">Edit</button>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div class="form-container" id="editForm" style="display: none;">
        <h2>Edit User</h2>
        <form method="POST">
            <input type="hidden" name="id" id="editId">
            <label for="username">Username:</label><br>
            <input type="text" name="username" id="editUsername" required><br><br>
            <label for="email">Email:</label><br>
            <input type="email" name="email" id="editEmail" required><br><br>
            <button type="submit" name="edit_user" class="btn">Save Changes</button>
        </form>
    </div>

    <script>
        function openEditForm(id, username, email) {
            document.getElementById('editForm').style.display = 'block';
            document.getElementById('editId').value = id;
            document.getElementById('editUsername').value = username;
            document.getElementById('editEmail').value = email;
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
