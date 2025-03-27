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
          <h2>SHORTS</h2>
          <select id="price-sort" class="price-sort">
            <option value="default">Sort by Price</option>
            <option value="low-to-high">Low to High</option>
            <option value="high-to-low">High to Low</option>
          </select>
        </div>
    
        <div class="products-grid" id="products-grid">
          <div class="product-card" data-price="1100">
            <a href="shorts/1.php">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS3.jpg?v=1734574023" alt="CIPHER SPLICED SHORTS - KHAKI/CREAM">
              <p>CIPHER SPLICED SHORTS - KHAKI/CREAM<br>₱1,100.00</p>
            </a>
          </div>
    
          <div class="product-card" data-price="1100">
            <a href="shorts/2.php">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS5.jpg?v=1734573884" alt="CIPHER SPLICED SHORTS - WHITE/GREY">
              <p>CIPHER SPLICED SHORTS - WHITE/GREY<br>₱1,100.00</p>
            </a>
          </div>
    
          <div class="product-card" data-price="1100">
            <a href="shorts/3.php">
              <img src="https://dbtkco.com/cdn/shop/files/SWIFTSHORTS1.jpg?v=1728698184" alt="SWIFT SHORTS - MULTI TONAL BLACK GRAY">
              <p>SWIFT SHORTS - MULTI TONAL BLACK GRAY<br>₱1,100.00</p>
            </a>
          </div>
    
          <div class="product-card" data-price="1100">
            <a href="shorts/4.php">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS1.jpg?v=1734573884" alt="CIPHER SPLICED SHORTS - BLACK/GRAY">
              <p>CIPHER SPLICED SHORTS - BLACK/GRAY<br>₱1,100.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1500">
            <a href="shorts/5.php">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHER_FLOCK_SHORTS_1.jpg?v=1724923454" alt="CIPHER FLOCK SHORTS - ACID WASHED BLACK">
              <p>CIPHER FLOCK SHORTS - ACID WASHED BLACK<br>₱1,500.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1500">
            <a href="shorts/6.php">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERFLOCKSHORTS2_d69e22f6-75e3-4bc8-99a9-161f48937d9c.jpg?v=1733981325" alt="CIPHER FLOCK SHORTS - ACID DARK GRAY">
              <p>CIPHER FLOCK SHORTS - ACID DARK GRAY<br>₱1,500.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1800">
            <a href="shorts/7.php">
              <img src="https://dbtkco.com/cdn/shop/files/GRANDPRIXSHORTS3.jpg?v=1733377627" alt="GRAND PRIX SHORTS - CREAM">
              <p>GRAND PRIX SHORTS - CREAM<br>₱1,800.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1500">
            <a href="shorts/8.php">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERFLOCKSHORTS2.jpg?v=1724922721" alt="CIPHER FLOCK SHORTS - ACID WASHED PINK">
              <p>CIPHER FLOCK SHORTS - ACID WASHED PINK<br>₱1,500.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1800">
            <a href="shorts/9.php">
              <img src="https://dbtkco.com/cdn/shop/files/GRANDPRIXSHORTS1.jpg?v=1733377627" alt="GRAND PRIX SHORTS - BLACK">
              <p>GRAND PRIX SHORTS - BLACK<br>₱1,800.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1500">
            <a href="shorts/10.php">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERFLOCKSHORTS1.jpg?v=1733981325" alt="CIPHER FLOCK SHORTS - ACID LIGHT GRAY">
              <p>CIPHER FLOCK SHORTS - ACID LIGHT GRAY<br>₱1,500.00</p>
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