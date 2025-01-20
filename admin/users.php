<?php
include('auth.php');

// Include database configuration
include '../config.php';


// Delete user if deleteid is provided in the URL
if (isset($_GET['deleteid'])) {
    $delete_id = intval($_GET['deleteid']);
    $sql = "DELETE FROM customer WHERE cust_id = $delete_id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header('Location: users.php');
        exit();
    } else {
        echo 'Error: ' . mysqli_error($conn);
    }
}

// Update user if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cust_name = $_POST['cust_name'];
    $cust_email = $_POST['cust_email'];
    $cust_phone = $_POST['cust_phone'];
    $cust_password = password_hash($_POST['cust_password'], PASSWORD_BCRYPT);  // Hash password
    $cust_address = $_POST['cust_address'];
    $cust_city = $_POST['cust_city'];
    $cust_zip = $_POST['cust_zip'];
    $cust_status = $_POST['cust_status']; // Assuming 'cust_status' is being submitted from the form

    $sql = "UPDATE customer SET 
                cust_name='$cust_name', 
                cust_email='$cust_email', 
                cust_phone='$cust_phone', 
                cust_password='$cust_password', 
                cust_address='$cust_address', 
                cust_city='$cust_city', 
                cust_zip='$cust_zip', 
                cust_status='$cust_status'
            WHERE cust_id=$cust_id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header('Location: users.php');
        exit();
    } else {
        echo 'Error: ' . mysqli_error($conn);
    }
}

// Fetch user data from the database
$stmt = $conn->prepare("SELECT * FROM customer WHERE cust_id = ?");
if ($stmt === false) {
    die('prepare() failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $cust_id);
$stmt->execute();
$result1 = $stmt->get_result();

// Check if the result contains data
if ($result1->num_rows > 0) {
    $row1 = $result1->fetch_assoc();
} else {
    $row1 = null;
}

// Fetch all users data from the database
$sql = "SELECT * FROM customer";
$result = mysqli_query($conn, $sql);

if ($result->num_rows > 0) {
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
} else {
    $rows = [];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/admin1.css">  
    <link rel="stylesheet" href="../css/admin2.css">
        
    <title>Users</title>
    <style>
        .dashbord-tables{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-Y-bottom  0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>

<body>
    <!-- =============== Navigation ================ -->
    <div class="container">
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <div class="logo-container">
                            <img src="../images/logo.png" alt="Logo" class="logo" />
                        </div>
                        <span class="title"></span>
                    </a>
                </li>
                <li>
                    <a href="dashboard.php">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="users.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Users</span>
                    </a>
                </li>
                <li>
                    <a href="sales-report.php">
                        <span class="icon">
                            <ion-icon name="cash-outline"></ion-icon>
                        </span>
                        <span class="title">Sales</span>
                    </a>
                </li>
                <li>
                    <a href="product/product.php">
                        <span class="icon">
                            <ion-icon name="cube-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Products</span>
                    </a>
                </li>
                <li>
                    <a href="product/flowers.php">
                        <span class="icon">
                            <ion-icon name="flower-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Flowers</span>
                    </a>
                </li>
                <li>
                    <a href="orders/order.php">
                        <span class="icon">
                            <ion-icon name="cart-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Orders</span>
                    </a>
                </li>
                <li>
                    <a href="customize/customize-order.php">
                        <span class="icon">
                        <ion-icon name="color-wand-outline"></ion-icon>
                        </span>
                        <span class="title"> Customize Orders</span>
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <span class="icon">
                            <ion-icon name="albums-outline"></ion-icon>
                        </span>
                        <span class="title">Categories</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- ========================= Main ==================== -->
        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>

                <div class="search">
                    <label>
                        <input type="text" placeholder="Search here">
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>

                <div class="user">
                    <img src="assets/imgs/customer01.jpg" alt="">
                </div>
            </div>

            <!-- ======================= Cards ================== -->
            <tr>
    <td colspan="7">
        <div class="abc scroll">
            <table width="93%" class="sub-table scrolldown" border="0">
                <thead>
                    <tr>
                        <th class="table-headin">Name</th>
                        <th class="table-headin">Email</th>
                        <th class="table-headin">Contact</th>
                        <th class="table-headin">Address</th>
                        <th class="table-headin">City</th>
                        <th class="table-headin">Status</th>
                        <th class="table-headin">Events</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, $sql);

                    if ($result->num_rows == 0) {
                        echo '<tr>
                                <td colspan="7">
                                    <center>
                                        <br><br><br><br>
                                        <p class="heading-main12" style="font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords!</p>
                                        <a class="non-style-link" href="users.php">
                                            <button class="login-btn btn-primary-soft btn">Go Back</button>
                                        </a>
                                    </center>
                                    <br><br><br><br>
                                </td>
                              </tr>';
                    } else {
                        while ($row = $result->fetch_assoc()) {
                            $cust_id = $row["cust_id"];
                            $cust_name = $row["cust_name"];
                            $cust_email = $row["cust_email"];
                            $cust_phone = $row["cust_phone"];
                            $cust_address = $row["cust_address"];
                            $cust_city = $row["cust_city"];
                            $cust_status = $row["cust_status"];
                            echo '<tr>
                                    <td>' . substr($cust_name, 0, 30) . '</td>
                                    <td>' . substr($cust_email, 0, 20) . '</td>
                                    <td>' . substr($cust_phone, 0, 20) . '</td>
                                    <td>' . substr($cust_address, 0, 20) . '</td>
                                    <td>' . substr($cust_city, 0, 20) . '</td>
                                    <td>' . substr($cust_status, 0, 20) . '</td>
                                    <td>
                                        <div style="display:flex;justify-content:center;">
                                            <a href="?action=edit&id=' . $cust_id . '&error=0" class="non-style-link">
                                                <button class="btn-primary-soft btn button-icon btn-edit" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;">Edit</button>
                                            </a>
                                            &nbsp;&nbsp;&nbsp;
                                            <a href="?action=view&id=' . $cust_id . '" class="non-style-link">
                                                <button class="btn-primary-soft btn button-icon btn-view" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;">View</button>
                                            </a>
                                           &nbsp;&nbsp;&nbsp;
                                           <a href="?action=drop&id=' . $cust_id . '&name=' . $cust_name . '" class="non-style-link">
                                                <button class="btn-primary-soft btn button-icon btn-delete" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;">Remove</button>
                                            </a>
                                        </div>
                                    </td>
                                  </tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </td>
</tr>



            </table>
        </div>
    </div>
    <?php
    if ($_GET) {
        $cust_id = $_GET["id"];
        $action = $_GET["action"];
        if ($action == 'drop') {
            $nameget = $_GET["cust_name"];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2>Are you sure?</h2>
                        <a class="close" href="users.php">&times;</a>
                        <div class="content">
                            You want to delete this record<br>(' . substr($nameget, 0, 40) . ').
                            
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <a href="delete-users.php?id=' . $cust_id . '" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Yes&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                        <a href="users.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;No&nbsp;&nbsp;</font></button></a>

                        </div>
                    </center>
            </div>
            </div>
            ';
        } elseif (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
            $cust_id = intval($_GET['id']);
            
            // Use a prepared statement
            $stmt = $conn->prepare("SELECT * FROM customer WHERE cust_id = ?");
            if ($stmt === false) {
                die('prepare() failed: ' . htmlspecialchars($conn->error));
            }
        
            $stmt->bind_param("i", $cust_id);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $cust_name = $row["cust_name"];
                $cust_email = $row["cust_email"];
                $cust_phone = $row["cust_phone"];
                $cust_address = $row["cust_address"];
                $cust_city = $row["cust_city"];
                $cust_zip = $row["cust_zip"];
        
            
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2></h2>
                        <a class="close" href="users.php">&times;</a>
                        <div class="content">
                            La Sorpresa by J&B<br>
                            
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        
                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details.</p><br><br>
                                </td>
                            </tr>
                            
                            <tr>
                                
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Name: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    ' . $cust_name . '<br><br>
                                </td>
                                
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Email" class="form-label">Email: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                ' . $cust_email . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="contact" class="form-label">Contact: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                ' . $cust_phone . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="address" class="form-label">Address: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                ' . $cust_address . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="city" class="form-label">City: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                ' . $cust_city . '<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="zip" class="form-label">ZIP: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                ' . $cust_zip . '<br><br>
                                </td>
                            </tr>                           


                            <tr>
                                <td colspan="2">
                                    <a href="users.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn" ></a>
                                
                                    
                                </td>
                
                            </tr>
                           

                        </table>
                        </div>
                    </center>
                    <br><br>
            </div>
            </div>
            ';
            }
        } elseif (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            $cust_id = intval($_GET['id']);


            $sqlmain = "SELECT * from customer where cust_id='$cust_id'";
            $result = $conn->query($sqlmain);
            $row = $result->fetch_assoc();
            $cust_name = $row["cust_name"];
            $cust_email = $row["cust_email"];
            $cust_phone = $row["cust_phone"];
            $cust_address = $row["cust_address"];
            $cust_city = $row["cust_city"];
            $cust_zip = $row["cust_zip"];
            $cust_status = $row['cust_status'];
            $error_1 = $_GET["error"];
            $errorlist = array(
                '1' => '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>',
                '2' => '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Confirmation Error! Reconfirm Password</label>',
                '3' => '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>',
                '4' => "",
                '0' => '',

            );

            if ($error_1 != '4') {
                echo '
                    <div id="popup1" class="overlay">
                            <div class="popup">
                            <center>
                            
                                <a class="close" href="users.php">&times;</a> 
                                <div style="display: flex;justify-content: center;">
                                <div class="abc">
                                <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                <tr>
                                        <td class="label-td" colspan="2">' .
                    $errorlist[$error_1]
                    . '</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Edit Users Details.</p>
                                        Users ID : ' . $cust_id . ' (Auto Generated)<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <form action="edit-users.php" method="POST" class="add-new-form">
                                            <label for="Email" class="form-label">Email: </label>
                                            <input type="hidden" value="' . $cust_id . '" name="cust_id00">
                                            <input type="hidden" name="oldemail" value="' . $cust_email . '" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                        <input type="email" name="cust_email" class="input-text" placeholder="Email Address" value="' . $cust_email . '" required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        
                                        <td class="label-td" colspan="2">
                                            <label for="cust_name" class="form-label">Name: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" name="cust_name" class="input-text" placeholder="Name" value="' . $cust_name . '" required><br>
                                        </td>
                                        
                                    </tr>
                                    
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="cust_phone" class="form-label">Contact: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" name="cust_phone" class="input-text" placeholder="Contact Number" value="' . $cust_phone . '" required><br>
                                        </td>
                                    </tr>                                    
                                    <tr>

                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="cust_address" class="form-label">Address: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" name="cust_address" class="input-text" placeholder="Address" value="' . $cust_address . '" required><br>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="cust_city" class="form-label">City: </label>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" name="cust_city" class="input-text" placeholder="City" value="' . $cust_city . '" required><br>
                                        </td>
                                    </tr>                                    
                                    <tr>

                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="cust_zip" class="form-label">ZIP: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" name="cust_zip" class="input-text" placeholder="ZIP" value="' . $cust_zip . '" required><br>
                                        </td>
                                    </tr>                                    

                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="cust_status" class="form-label">Status: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" name="cust_status" class="input-text" placeholder="Status" value="' . $cust_status. '" required><br>
                                        </td>
                                    </tr>                                    
                                    <tr>

                                        <td colspan="2">
                                            <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                            <input type="submit" value="Save" class="login-btn btn-primary btn">
                                        </td>
                        
                                    </tr>
                                
                                    </form>
                                    </tr>
                                </table>
                                </div>
                                </div>
                            </center>
                            <br><br>
                    </div>
                    </div>
                    ';
            } else {
                echo '
                <div id="popup1" class="overlay">
                        <div class="popup">
                        <center>
                        <br><br><br><br>
                            <h2>Edit Successfully!</h2>
                            <a class="close" href="users.php">&times;</a>
                            <div class="content">
                                
                                
                            </div>
                            <div style="display: flex;justify-content: center;">
                            
                            <a href="users.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>

                            </div>
                            <br><br>
                        </center>
                </div>
                </div>
    ';



            }
            ;
        }
        ;
    }
    ;

    ?>
    </div>

    <!-- =========== Scripts =========  -->
    <script src="assets/js/main.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
<style>
    /* Make the container scrollable */
.abc.scroll {
    overflow-x: auto;
    max-width: 100%; /* Adjust based on container size */
}

/* Table Style */
.sub-table {
    width: 100%;
    border-collapse: collapse;
}

.sub-table th, .sub-table td {
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
}

.sub-table th {
    background-color: #f8f9fa;
}

.sub-table td {
    word-wrap: break-word;
}

/* Optional: You can add more specific styles for the rows or columns as needed */
</style>
</html>