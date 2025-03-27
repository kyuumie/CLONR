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
          <h2>ACCESSORIES</h2>
          <select id="price-sort" class="price-sort">
            <option value="default">Sort by Price</option>
            <option value="low-to-high">Low to High</option>
            <option value="high-to-low">High to Low</option>
          </select>
        </div>
    
        <div class="products-grid" id="products-grid">
          <div class="product-card" data-price="300">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/GRANDPRIXENAMELPIN.png?v=1733378380" alt="GRAND PRIX ENAMEL PIN">
              <p>GRAND PRIX ENAMEL PIN<br>₱300.00</p>
            </a>
          </div>
    
          <div class="product-card" data-price="350">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/HYPERGARAGESTICKERPACK.jpg?v=1732590086" alt="HYPER GARAGE STICKER PACK">
              <p>HYPER GARAGE STICKER PACK<br>₱350.00</p>
            </a>
          </div>
    
          <div class="product-card" data-price="300">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/HYPERGARAGEWOVENTAG1.jpg?v=1732589996" alt="HYPER GARAGE WOVEN KEYCHAIN">
              <p>HYPER GARAGE WOVEN KEYCHAIN<br>₱300.00</p>
            </a>
          </div>
    
          <div class="product-card" data-price="350">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/HYPERGARAGEMETALKEYCHAIN.jpg?v=1732589925" alt="HYPER GARAGE METAL KEYCHAIN">
              <p>HYPER GARAGE METAL KEYCHAIN<br>₱350.00</p>
            </a>
          </div>

          <div class="product-card" data-price="300">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/products/2_2_1.jpg?v=1677252535" alt="DBTK HOLOGRAPHIC STICKER PACK">
              <p>DBTK HOLOGRAPHIC STICKER PACK<br>₱300.00</p>
            </a>
          </div>

          <div class="product-card" data-price="950">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/DBTKEVERMORESLINGBAG1.jpg?v=1734082111" alt="DBTK EVERMORE SLING BAG - BLACK">
              <p>DBTK EVERMORE SLING BAG - BLACK<br>₱950.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1100">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/WOODLAND_CIPHER_FLASK_1.jpg?v=1721960065" alt="WOODLAND CIPHER FLASK - BLACK/GRAY">
              <p>WOODLAND CIPHER FLASK - BLACK/GRAY<br>₱1,100.00</p>
            </a>
          </div>

          <div class="product-card" data-price="3500">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERFLOCKHOODIE1.jpg?v=1737105926" alt="CIPHER FLOCK HOODIE - BLACK">
              <p>CIPHER FLOCK HOODIE - BLACK<br>₱3,500.00</p>
            </a>
          </div>

          <div class="product-card" data-price="900">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/WOODLANDCIPHERUMBRELLA1.jpg?v=1721958145" alt="WOODLAND CIPHER UMBRELLA">
              <p>WOODLAND CIPHER UMBRELLA<br>₱900.00</p>
            </a>
          </div>

          <div class="product-card" data-price="1350">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/BAG1_9bd676b9-3db7-4ea8-adf7-190d5d446be7.jpg?v=1716898728" alt="DBTK SLANT BODY BAG">
              <p>DBTK SLANT BODY BAG<br>₱1,350.00</p>
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