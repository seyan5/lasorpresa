<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/shopcart.css">
  <title>Shopping Cart</title>
</head>
<body>
    <div class="header">
        <a href="#" class="back-link">
            <span class="back-arrow">&lt;</span> La Sorpresa Home Page
        </a>
    </div>
  <div class="container">
    <div class="cart">
      <hr>
      <h3>Shopping cart</h3>
      <p>You have 3 items in your cart</p>
      <div class="cart-item">
        <img src="../ivd/flower.png" alt="Tulip Bouquet">
        <div>
          <h4>test</h4>
          <p>4 pcs. Tulips</p>
        </div>
        <div class="quantity">
          <input type="number" value="1" min="1">
        </div>
        <p>₱2,000</p>
        <button class="delete">🗑️</button>
      </div>
      <div class="cart-item">
        <img src="../ivd/flower.png" alt="Red Rose Bouquet">
        <div>
          <h4>test</h4>
          <p>4 pcs. Red Rose</p>
        </div>
        <div class="quantity">
          <input type="number" value="1" min="1">
        </div>
        <p>₱2,000</p>
        <button class="delete">🗑️</button>
      </div>
      <div class="cart-item">
        <img src="../ivd/flower.png" alt="Red Rose Bouquet">
        <div>
          <h4>test</h4>
          <p>5 pcs. Red Rose</p>
        </div>
        <div class="quantity">
          <input type="number" value="1" min="1">
        </div>
        <p>₱2,500</p>
        <button class="delete">🗑️</button>
      </div>
    </div>
    <div class="payment">
      <h3>Card Details</h3>
      <p>Mode of Payment</p>
      <div class="payment-options">
        <img src="" alt="GCash">
        <span>Cash On Pick Up</span>
      </div>
      <form>
        <label>Email</label>
        <input type="email" placeholder="E-mail">
        <label>Contact Number</label>
        <input type="text" placeholder="Number">
        <label>Account Name</label>
        <input type="text" placeholder="Account Name">
      </form>
      <hr>
      <div class="summary">
        <p>Subtotal <span>₱0</span></p>
        <p>Shipping <span>₱0</span></p>
        <p>Total (Tax incl.) <span>₱0</span></p>
      </div>
      <button class="checkout">₱0 Checkout &gt;</button>
    </div>
  </div>
</body>
</html>
