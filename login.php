<?php
session_start();
include 'config.php'; // Include your database connection file

date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

$error = ''; // Initialize error variable
$success = ''; // Initialize success variable

if(isset($_POST['form1'])) {
        
    if(empty($_POST['cust_email']) || empty($_POST['cust_password'])) {
        $error_message = LANG_VALUE_132.'<br>';
    } else {
        
        $cust_email = strip_tags($_POST['cust_email']);
        $cust_password = strip_tags($_POST['cust_password']);

        $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
        $statement->execute(array($cust_email));
        $total = $statement->rowCount();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach($result as $row) {
            $cust_status = $row['cust_status'];
            $row_password = $row['cust_password'];
        }

        if($total==0) {
            $error_message .= LANG_VALUE_133.'<br>';
        } else {
            //using MD5 form
            if( $row_password != md5($cust_password) ) {
                $error_message .= LANG_VALUE_139.'<br>';
            } else {
                if($cust_status == 0) {
                    $error_message .= LANG_VALUE_148.'<br>';
                } else {
                    $_SESSION['customer'] = $row;
                    header("location: ".BASE_URL."dashboard.php");
                }
            }
            
        }
    }
}


// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in Sign up form</title>
    <!-- font awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g =="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- css stylesheet -->
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <div class="logo-container">
        <img src="images/logo.png" alt="Logo" class="logo" />
    </div>

    <!-- Flower Image -->
    <div class="flower-container">
        <img src="images/flower2.png" alt="Flower" class="flower" />
    </div>

    <div class="container" id="container">
        <div class="form-container sign-up-container">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <h1>Create Account</h1>
                <span>or use your email for registration</span>
                <div class="infield">
                    <label for="name" class="form-label"></label>
                    <input type="text" name="firstname" placeholder="First Name" required>
                    <label></label>
                </div>
                <div class="infield">
                    <label for="name" class="form-label"></label>
                    <input type="text" name="lastname" placeholder="Last Name" required>
                    <label></label>
                </div>
                <div class="infield">
                    <label for="useremail" class="form-label"></label>
                    <input type="text" name="username" placeholder="Username" required>
                    <label></label>
                </div>
                <div class="infield">
                    <label for="email" class="form-label"></label>
                    <input type="email" placeholder="Email" name="email" required />
                    <label></label>
                </div>
                <div class="infield">
                    <label for="contact" class="form-label"></label>
                    <input type="text" name="contact" class="input-text" placeholder="Contact Number" required>
                    <label></label>
                </div>
                <div class="infield">
                    <input type="password" name="password" placeholder="Password" required>
                    <label></label>
                </div>
                <div class="infield">
                    <input type="password" name="confirmpassword" class="input-text" placeholder="Confirm Password" required>
                    <label></label>
                </div>
                <button type="submit" name="register">Sign Up</button>
                <?php if ($success) echo "<p class='success'>$success</p>"; ?>
                <?php if ($error) echo "<p class='error'>$error</p>"; ?>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="#" method="POST" class="form-body">
                <h1>Sign in</h1>
                <div class="tulip-container">
                    <img src="images/flower1.png" alt="Flower" class="tulip" />
                </div>
                <div class="infield">
                    <label for="useremail" class="form-label"></label>
                    <input type="text" placeholder="Email or Username" name="usernameoremail" required>
                    <label></label>
                </div>
                <div class="infield">
                    <label for="password" class="form-label"></label>
                    <input type="password" placeholder="Password" name="password" required>
                    <label></label>
                </div>
                <a href="#" class="forgot">Forgot your password?</a>
                <button type="submit" name="login">Sign In</button>
                <?php if ($error) echo "<p class='error'>$error</p>"; ?>
            </form>
        </div>
        <div class="overlay-container" id="overlayCon">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info.</p>
                    <button id="signIn">Sign In</button>
                    
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start your journey with us.</p>
                    <button id="signUp">Sign Up</button>
                </div>
            </div>
            <button id="overlayBtn"></button>
        </div>
    </div>

    <!-- js code -->
    <script>
        const container = document.getElementById('container');
        const signInButton = document.getElementById('signIn');
        const signUpButton = document.getElementById('overlayBtn');
        
        signInButton.addEventListener('click', () => {
            console.log("Sign In button clicked");
            container.classList.remove('right-panel-active');
        });
        signUpButton.addEventListener('click', () => {
            console.log("Sign Up button clicked");
            container.classList.add('right-panel-active');
        });
    </script>

</body>

</html>