<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title>CLONR</title>
        <link rel="stylesheet" href="../../global.css"/>
        <link rel="stylesheet" href="../addtocart.css">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    </head>
    
    <body>
    <header>
          <div class="container">
            <a href="../../index.php"><h1 class="title">CLONR</h1></a>
            <nav>
              <ul class="navbar">
                <li><a href="../../index.php">HOME</a></li>
                <li class="dropdown">SHOP
                  <ul class="dropdown-menu">
                    <li><a href="../tshirts.php">T-shirts</a></li>
                    <li><a href="../jackets.php">Jackets</a></li>
                    <li><a href="../pants.php">Pants</a></li>
                    <li><a href="../shorts.php">Shorts</a></li>
                    <li><a href="../accessories.php">Accessories</a></li>
                  </ul>
                </li>
                <li><a href="../../sale.php">SALE</a></li>
                <li><a href="../../sizechart.php">SIZE CHART</a></li>
                <li><a href="../../contact.php">CONTACT US</a></li>
              </ul>
            </nav>
          </div>
      </header>

      <section class="product-section">
        <div class="product-details">
          <h2>T-SHIRTS</h2>
          <h1>Nationals Fruits Tee - White</h1>
          <p>₱1,600.00</p>
          <label for="size">Size:</label>
          <select id="size">
            <option>Small</option>
            <option>Medium</option>
            <option>Large</option>
            <option>X-Large</option>
            <option>XX-Large</option>
          </select>
    
          <label for="quantity">Quantity:</label>
          <select id="quantity">
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
          </select>
    
          <button id="add-to-cart">Add to Cart</button>
        </div>
    
        <div class="product-slider">
          <button class="arrow left" onclick="prevSlide()">&#9664;</button>
          <img id="slider-image" src="https://dbtkco.com/cdn/shop/files/NationalsFruitsTee1.jpg?v=1733130710" alt="Cipher Tee">
          <button class="arrow right" onclick="nextSlide()">&#9654;</button>
        </div>
      </section>

      <script>
        const images = [
        "https://dbtkco.com/cdn/shop/files/NationalsFruitsTee1.jpg?v=1733130710",
        "https://dbtkco.com/cdn/shop/files/NationalsFruitsTee2.jpg?v=1733130710",
        "https://dbtkco.com/cdn/shop/files/dbtknew-7108.jpg?v=1733130710"
        ];

        let currentIndex = 0;
        const sliderImage = document.getElementById('slider-image');

        function prevSlide() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        sliderImage.src = images[currentIndex];
        }

        function nextSlide() {
        currentIndex = (currentIndex + 1) % images.length;
        sliderImage.src = images[currentIndex];
        }

        document.getElementById('add-to-cart').addEventListener('click', () => {
        const selectedSize = document.getElementById('size').value;
        const selectedQuantity = document.getElementById('quantity').value;
        alert(`Added ${selectedQuantity} x Size ${selectedSize} to Cart`);
        });
      </script>

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