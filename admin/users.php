<?php
// users.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lasorpresa";

    $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $email, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php");
    exit;
}

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
