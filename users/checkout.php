<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>

    <!-- Font Awesome and Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/checkout.css">
</head>
<body>
    <!-- Header -->
    <header>
        <input type="checkbox" id="toggler">
        <label for="toggler" class="fas fa-bars"></label>

        <a href="#" class="logo">Flower<span>.</span></a>
        <nav class="navbar">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#product">Product</a>
            <a href="#occasion">Occasion</a>
            <a href="#review">Review</a>
            <a href="#contacts">Contacts</a>
        </nav>

        <div class="icons">
            <a href="#" class="fas fa-heart"></a>
            <a href="cart.html" class="fas fa-shopping-cart"></a>
            <a href="#" class="fas fa-user"></a>
        </div>
    </header>

    <!-- Main Section -->
    <section>
        <div class="container">
            <div class="checkoutLayout">
                <!-- Shopping Cart -->
                <div>
                    <h2>My Shopping Cart</h2>
                    <div class="cart">
                        <div class="cart-item">
                            <div class="row">
                                <div class="col-md-7 center-item">
                                    <img src="../ivd/flower.png" alt="iPhone 11">
                                    <h5>iPhone 11 128GB Black ($1219)</h5>
                                </div>
                                <div class="col-md-5 center-item">
                                    <div class="input-group number-spinner">
                                        <button class="btn btn-default" id="phone-minus"><i class="fas fa-minus"></i></button>
                                        <input id="phone-number" type="number" min="0" class="form-control text-center" value="1">
                                        <button class="btn btn-default" id="phone-plus"><i class="fas fa-plus"></i></button>
                                    </div>
                                    <h5>$<span id="phone-total">1219</span></h5>
                                        <img src="images/remove.png" alt="Remove" class="remove-item">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Checkout Form -->
                <div class="right">
                    <h1>Checkout</h1>
                    <form class="form">
                        <div class="group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" placeholder="Enter your name">
                        </div>
                        <div class="group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" placeholder="Enter your phone number">
                        </div>
                        <div class="group">
                            <label for="address">Address</label>
                            <input type="text" id="address" placeholder="Enter your address">
                        </div>
                        <div class="group">
                            <label for="country">Country</label>
                            <select id="country">
                                <option value="">Choose...</option>
                                <option value="kingdom">Kingdom</option>
                            </select>
                        </div>
                        <div class="group">
                            <label for="city">City</label>
                            <select id="city">
                                <option value="">Choose...</option>
                                <option value="london">London</option>
                            </select>
                        </div>
                    </form>
                    <div class="return">
                        <div class="row">
                            <div>Total Quantity</div>
                            <div class="totalQuantity">$<span id="total-price">1278</div>
                        </div>
                        <div class="row">
                            <div>Subtotal: </div>
                            <div class="totalPrice">$<span id="sub-total">1278</div>
                        </div>
                    </div>
                    <button class="buttonCheckout">CHECKOUT</button>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript -->
    <script src="js/checkout.js"></script>
</body>
</html>
