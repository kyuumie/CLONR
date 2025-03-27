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
          <h2>T-SHIRTS</h2>
          <select id="price-sort" class="price-sort">
            <option value="default">Sort by Price</option>
            <option value="low-to-high">Low to High</option>
            <option value="high-to-low">High to Low</option>
          </select>
        </div>
    
        <div class="products-grid" id="products-grid">
          <div class="product-card" data-price="1000">
            <a href="tshirts/1.php">
              <img src="https://dbtkco.com/cdn/shop/files/SidePocketBlackShirtFront.jpg?v=1742283606" alt="Cipher Tee Black and Neon Green">
              <p>CIPHER TEE 2025 - BLACK AND NEON GREEN<br>₱1,000.00</p>
            </a>
          </div>
    
          <div class="product-card" data-price="1000">
            <a href="tshirts/2.php">
              <img src="https://dbtkco.com/cdn/shop/files/Cipher_Black_and_White_Shirt_Front.jpg?v=1742283658" alt="CIPHER TEE 2025 - BLACK AND WHITE">
              <p>CIPHER TEE 2025 - BLACK AND WHITE<br>₱1,000.00</p>
            </a>
          </div>
    
          <div class="product-card" data-price="1100">
            <a href="tshirts/3.php">
              <img src="https://dbtkco.com/cdn/shop/files/Slant_Cream_Shirt_Front.jpg?v=1742283821" alt="SLANT TEE 2025 - CREAM AND BLACK">
              <p>SLANT TEE 2025 - CREAM AND BLACK<br>₱1,100.00</p>
            </a>
          </div>
    
          <div class="product-card" data-price="1100">
            <a href="tshirts/4.php">
              <img src="https://dbtkco.com/cdn/shop/files/Slant_Black_White_Shirt_Front.jpg?v=1742283943" alt="SLANT TEE 2025 - BLACK AND WHITE">
              <p>SLANT TEE 2025 - BLACK AND WHITE<br>₱1,100.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1500">
            <a href="tshirts/5.php">
              <img src="https://dbtkco.com/cdn/shop/files/MOBV21.jpg?v=1735981287" alt="MOB V2 TEE - BROWN">
              <p>MOB V2 TEE - BROWN<br>₱1,500.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1450">
            <a href="tshirts/6.php">
              <img src="https://dbtkco.com/cdn/shop/files/DBTKARCTEE3.jpg?v=1715245989" alt="DBTK ARC TEE - WHITE">
              <p>DBTK ARC TEE - WHITE<br>₱1,450.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1000">
            <a href="tshirts/7.php">
              <img src="https://dbtkco.com/cdn/shop/files/COUPE3.jpg?v=1732697599" alt="COUPE TEE - WHITE">
              <p>COUPE TEE - WHITE<br>₱1,000.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1600">
            <a href="tshirts/8.php">
              <img src="https://dbtkco.com/cdn/shop/files/NationalsFruitsTee1.jpg?v=1733130710" alt="Nationals Fruits Tee - White">
              <p>Nationals Fruits Tee - White<br>₱1,600.00</p>
            </a>
          </div>

          <div class="product-card" data-price="950">
            <a href="tshirts/9.php">
              <img src="https://dbtkco.com/cdn/shop/files/INFINITECHASE3.jpg?v=1733898703" alt="INFINITE CHASE TEE - WHITE">
              <p>INFINITE CHASE TEE - WHITE<br>₱950.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1500">
            <a href="tshirts/10.php">
              <img src="https://dbtkco.com/cdn/shop/files/MOBV25.jpg?v=1735981139" alt="MOB V2 TEE - PURPLE">
              <p>MOB V2 TEE - PURPLE<br>₱1,500.00</p>
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