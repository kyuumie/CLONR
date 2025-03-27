<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title>CLONR</title>
        <link rel="stylesheet" href="../global.css"/>
        <link rel="stylesheet" href="products.css">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    </head>
    
    <body>
      <header>
          <div class="container">
            <a href="../index.php"><h1 class="title">CLONR</h1></a>
            <nav>
              <ul class="navbar">
                <li><a href="../index.php">HOME</a></li>
                <li class="dropdown">SHOP
                  <ul class="dropdown-menu">
                    <li><a href="tshirts.php">T-shirts</a></li>
                    <li><a href="jackets.php">Jackets</a></li>
                    <li><a href="pants.php">Pants</a></li>
                    <li><a href="shorts.php">Shorts</a></li>
                    <li><a href="accessories.php">Accessories</a></li>
                  </ul>
                </li>
                <li><a href="../sale.php">SALE</a></li>
                <li><a href="../sizechart.php">SIZE CHART</a></li>
                <li><a href="../contact.php">CONTACT US</a></li>
              </ul>
            </nav>
          </div>
      </header>
    
      <section class="products-section">
        <div class="section-header">
          <h2>JACKETS</h2>
          <select id="price-sort" class="price-sort">
            <option value="default">Sort by Price</option>
            <option value="low-to-high">Low to High</option>
            <option value="high-to-low">High to Low</option>
          </select>
        </div>
    
        <div class="products-grid" id="products-grid">
          <div class="product-card" data-price="2300">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHER_STREAK_CREWNECK_1.jpg?v=1728695354" alt="CIPHER STREAK CREWNECK - MID GRAY/ OFF WHITE">
              <p>CIPHER STREAK CREWNECK - MID GRAY/ OFF WHITE<br>₱2,300.00</p>
            </a>
          </div>

          <div class="product-card" data-price="2300">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERSTREAKCREWNECK3.jpg?v=1728695291" alt="CIPHER STREAK CREWNECK - BROWN CREAM">
              <p>CIPHER STREAK CREWNECK - BROWN CREAM<br>₱2,300.00</p>
            </a>
          </div>

          <div class="product-card" data-price="3000">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/D-SPARKPANELEDJACKET5.jpg?v=1728696742" alt="D-SPARK PANELED JACKET - CREAM BEIGE">
              <p>D-SPARK PANELED JACKET - CREAM BEIGE<br>₱3,000.00</p>
            </a>
          </div>

          <div class="product-card" data-price="2800">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/OAKSHADEWORKWEARJACKET1.jpg?v=1737095917" alt="OAKSHADE WORKWEAR JACKET">
              <p>OAKSHADE WORKWEAR JACKET<br>₱2,800.00</p>
            </a>
          </div>

          <div class="product-card" data-price="3300">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/FULL-ZIP_SPARK_3.jpg?v=1737099889" alt="SPARK PANELED JACKET - BLACK">
              <p>SPARK PANELED JACKET - BLACK<br>₱3,300.00</p>
            </a>
          </div>

          <div class="product-card" data-price="3800">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/Artboard3_c03fdea3-ed94-4eb1-9c79-da90934744c5.jpg?v=1734081482" alt="COMPILATION HOODIE - OFF-WHITE">
              <p>COMPILATION HOODIE - OFF-WHITE<br>₱3,800.00</p>
            </a>
          </div>

          <div class="product-card" data-price="3300">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/FULL-ZIPSPARK1.jpg?v=1737100793" alt="SPARK PANELED JACKET - BLUEBERRY">
              <p>SPARK PANELED JACKET - BLUEBERRY<br>₱3,300.00</p>
            </a>
          </div>

          <div class="product-card" data-price="3500">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERFLOCKHOODIE1.jpg?v=1737105926" alt="CIPHER FLOCK HOODIE - BLACK">
              <p>CIPHER FLOCK HOODIE - BLACK<br>₱3,500.00</p>
            </a>
          </div>

          <div class="product-card" data-price="3300">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/MERGEHALF-ZIP3.jpg?v=1737119068" alt="MERGE HALF-ZIP SWEATSHIRT - LIGHT GRAY">
              <p>MERGE HALF-ZIP SWEATSHIRT - LIGHT GRAY<br>₱3300.00</p>
            </a>
          </div>

          <div class="product-card" data-price="3800">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/Artboard1_1_fb285510-ecd2-42ab-8733-c55d2a5f4a48.jpg?v=1734081501" alt="COMPILATION HOODIE - BLACK">
              <p>COMPILATION HOODIE - BLACK<br>₱3,800.00</p>
            </a>
          </div>
        </div>
      </section>
  
      <hr class="custom-hr">

      <footer>
        <div class="footer-content">
            <h2>CLONR - Wear the Movement</h2>
            <p>Email: customerservice.CLONR@gmail.com | Phone: +63 XXX XXX XXXX</p>
            <p>© 2025 CLONR. All Rights Reserved.</p>
        </div>
      </footer>

      <script>
        document.getElementById('price-sort').addEventListener('change', function() {
          const productsGrid = document.getElementById('products-grid');
          const products = Array.from(productsGrid.getElementsByClassName('product-card'));
          const sortOption = this.value;
    
          if (sortOption === 'low-to-high') {
            products.sort((a, b) => a.getAttribute('data-price') - b.getAttribute('data-price'));
          } else if (sortOption === 'high-to-low') {
            products.sort((a, b) => b.getAttribute('data-price') - a.getAttribute('data-price'));
          }
    
          products.forEach(product => productsGrid.appendChild(product));
        });
      </script>
    </body>
</html>