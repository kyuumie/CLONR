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
          <h2>PANTS</h2>
          <select id="price-sort" class="price-sort">
            <option value="default">Sort by Price</option>
            <option value="low-to-high">Low to High</option>
            <option value="high-to-low">High to Low</option>
          </select>
        </div>
    
        <div class="products-grid" id="products-grid">
          <div class="product-card" data-price="2500">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/OAKSHADEWIDEPANTS1.jpg?v=1737096791" alt="OAKSHADE WIDE PANTS">
              <p>OAKSHADE WIDE PANTS<br>₱2,500.00</p>
            </a>
          </div>

          <div class="product-card" data-price="2700">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/D-SPARKPANELEDPANTS3.jpg?v=1728696742" alt="D-SPARK PANELED PANTS - CREAM BEIGE">
              <p>D-SPARK PANELED PANTS - CREAM BEIGE<br>₱2,700.00</p>
            </a>
          </div>
    
          <div class="product-card" data-price="5995">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/RACING_PANTS.jpg?v=1741421203" alt="RACING PANTS">
              <p>RACING PANTS<br>₱5,995.00</p>
            </a>
          </div>
    
          <div class="product-card" data-price="2300">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/MERGE_WIDE_PANTS_1.jpg?v=1737099107" alt="MERGE WIDE PANTS - BLACK">
              <p>MERGE WIDE PANTS - BLACK<br>₱2,300.00</p>
            </a>
          </div>

          <div class="product-card" data-price="2300">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/MERGEWIDEPANTS3.jpg?v=1737099509" alt="MERGE WIDE PANTS - LIGHT GRAY">
              <p>MERGE WIDE PANTS - LIGHT GRAY<br>₱2,300.00</p>
            </a>
          </div>

          <div class="product-card" data-price="2500">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/SPARKPANELEDWIDEPANTS3.jpg?v=1737103509" alt="SPARK PANELED WIDE PANTS - BLACK">
              <p>SPARK PANELED WIDE PANTS - BLACK<br>₱2,500.00</p>
            </a>
          </div>

          <div class="product-card" data-price="2500">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/SPARK_PANELED_WIDE_PANTS_1.jpg?v=1737105339" alt="SPARK PANELED WIDE PANTS - BLUEBERRY">
              <p>SPARK PANELED WIDE PANTS - BLUEBERRY<br>₱2,500.00</p>
            </a>
          </div>

          <div class="product-card" data-price="2500">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERFLOCKWIDEPANTS1.jpg?v=1737106488" alt="CIPHER FLOCK PANTS - BLACK">
              <p>CIPHER FLOCK PANTS - BLACK<br>₱2,500.00</p>
            </a>
          </div>

          <div class="product-card" data-price="3300">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHER_FLOCK_WIDE_PANTS_4.jpg?v=1737106328" alt="CIPHER FLOCK PANTS - BROWN">
              <p>CIPHER FLOCK PANTS - BROWN<br>₱2500.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1450">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/90_SODYSSEY4.jpg?v=1736502330" alt="90’S ODYSSEY PANTS">
              <p>90’S ODYSSEY PANTS<br>₱1,450.00</p>
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