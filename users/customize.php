<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flower Customization</title>
  <link rel="stylesheet" href="../css/customize.css">
</head>
<body>
  <div class="container">
    <header class="header">
      <a href="#" class="back-link">
        <span class="back-arrow">←</span> La Sorpresa Home Page
      </a>
    </header>
    
    <main class="main-content">
      <section class="image-section">
        <h2>Customization</h2>
        <img src="../ivd/flower.png" alt="Flower Bouquet" class="bouquet-image">
      </section>
      <section class="form-section">
        <h2>Customize your own bouquet</h2>
        <form class="customization-form">
          <div class="select-group">
            <div>
              <label for="typeOfFlower">Type of Flower</label>
              <select id="typeOfFlower">
                <option>Rose</option>
                <option>Tulip</option>
                <option>Lily</option>
              </select>
            </div>
            <div>
              <label for="numberOfFlowers">Number of Flowers</label>
              <input type="number" id="numberOfFlowers" value="3">
            </div>
          </div>
      
          <!-- New container for size, add another flower, and number of added flowers -->
          <div class="row-container">
            <div class="size-group">
              <label for="size">Size</label>
              <div class="radio-buttons">
                <label><input type="radio" name="size" value="medium"> Medium</label>
                <label><input type="radio" name="size" value="large" checked> Large</label>
                <label><input type="radio" name="size" value="grande"> Grande</label>
              </div>
            </div>
      
            <div class="flower-group">
              <label for="addAnotherFlower">Add Another Flower</label>
              <select id="addAnotherFlower">
                <option value="">Select a Flower</option>
                <option>Sunflower</option>
                <option>Orchid</option>
                <option>Carnation</option>
              </select>
              
              <label for="additionalFlowerCount">Number of Added Flowers</label>
              <input type="number" id="additionalFlowerCount" value="2">
            </div>
          </div>
      
          <label for="wrapperColor">Wrapper Color</label>
          <select id="wrapperColor">
            <option>Red</option>
            <option>Blue</option>
            <option>Pink</option>
          </select>
      
          <label for="customizedCard">Customized Card</label>
          <textarea id="customizedCard" placeholder="Happy Monthsary"></textarea>
      
          <div class="totals">
            <p>Subtotal: <span>₱4,500</span></p>
            <p>Shipping: <span>₱223</span></p>
            <p>Total (Tax incl.): <span>₱6,723</span></p>
          </div>
      
          <button type="button" class="btn check-receipt">Check Receipt →</button>
          <button type="submit" class="btn checkout">₱4,723 Checkout →</button>
        </form>
      </section>
    </main>
  </div>
</body>
</html>
