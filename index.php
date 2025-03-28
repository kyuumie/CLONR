<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title>CLONR</title>
        <link rel="stylesheet" href="global.css"/>
        <link rel="stylesheet" href="index.css">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    </head>
    
    <body>
      <header>
          <div class="container">
            <a href="index.php"><h1 class="title">CLONR</h1></a>
            <nav>
              <ul class="navbar">
                <li class="dropdown"><a href="index.php">HOME</a>
                  <ul class="dropdown-menu">
                    <li><a href="login.php">Login</a></li>
                  </ul>
                </li>
                <li class="dropdown">SHOP
                  <ul class="dropdown-menu">
                    <li><a href="products/tshirts.php">T-shirts</a></li>
                    <li><a href="products/jackets.php">Jackets</a></li>
                    <li><a href="products/pants.php">Pants</a></li>
                    <li><a href="products/shorts.php">Shorts</a></li>
                    <li><a href="products/accessories.php">Accessories</a></li>
                  </ul>
                </li>
                <li><a href="sale.php">SALE</a></li>
                <li><a href="sizechart.php">SIZE CHART</a></li>
                <li><a href="contact.php">CONTACT US</a></li>
              </ul>
            </nav>
          </div>
      </header>

      <div class="slider-container slide-up">
        <input type="radio" name="slider" id="slide1" checked>
        <input type="radio" name="slider" id="slide2">
        <input type="radio" name="slider" id="slide3">
        <input type="radio" name="slider" id="slide4">
        
        <div class="slides">
          <div class="slide">
              <img src="https://scontent.fmnl4-3.fna.fbcdn.net/v/t39.30808-6/463406894_2801136693393736_7920077726519743928_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=cf85f3&_nc_eui2=AeFfrQ5Y3Pt5x4EdwnAFo_4-aqAYbdb7z1hqoBht1vvPWDU0ll_7uyfmEQxve683MmO7VgiPUE5m_o8Lbgz-559M&_nc_ohc=AE0hXX1CdUMQ7kNvgF7WAR5&_nc_oc=AdnhTe7jq8NKmQeqY6qXEphStc4MfBP9mSDTov7_3kklhfp9vL2o8to17aB6-GEqezM&_nc_zt=23&_nc_ht=scontent.fmnl4-3.fna&_nc_gid=yCxrUhfr5HJtPlvbQcH_VQ&oh=00_AYF-0AAPZth8nkAIIRGCuuoCEbCpmR7HmeAsnatGrp868g&oe=67E8971D" alt="D">
          </div>
          <div class="slide">
            <img src="https://scontent.fmnl4-3.fna.fbcdn.net/v/t39.30808-6/463206989_2801136713393734_787274631427919053_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=cf85f3&_nc_eui2=AeFCP5BvYLSrHWDO0XF1AFEoY_-3EokpAW1j_7cSiSkBbQy6E0z9mtBo3iBmEed4TcWxtEploSg5zQP4nEDz5tFn&_nc_ohc=J2InGRXz1k8Q7kNvgEKU-sD&_nc_oc=AdmgqQESKEk-6VzNylqZNV7oTAxBzMlknoGasPOXdz4MhNf7sf0wbZoAnodGkxp8vqs&_nc_zt=23&_nc_ht=scontent.fmnl4-3.fna&_nc_gid=kmJmqHJuVbx5xVDbAV1Aow&oh=00_AYF5sftm-T3WxQxv1UVbN9PQS1f4d_-juPbKcu4_sgEtUg&oe=67E863CF" alt="D">
          </div>
          <div class="slide">
            <img src="https://scontent.fmnl4-2.fna.fbcdn.net/v/t1.6435-9/59866642_1225481554292599_1472663201110818816_n.jpg?_nc_cat=105&ccb=1-7&_nc_sid=cf85f3&_nc_eui2=AeEUv-nAnZcjqaTzXEwp0cRNRU13i5CN9ENFTXeLkI30Q643ZNWeoFotzGsZxgaULLRp0sQo3ZZqOgsQDOJEsTZj&_nc_ohc=oJQoez6ALCYQ7kNvgHICcE7&_nc_oc=AdnMzyOYAjIIEqEHeXYM0pVEZTEzi1NY3aCZDAsYz22xDAlHbc1EdETBQlH6Nu3Tf-k&_nc_zt=23&_nc_ht=scontent.fmnl4-2.fna&_nc_gid=LP9XDmHnJ5RQU1Hn5F754A&oh=00_AYGTVK9jghx4d3Gai4UW85BeUz-gedMTmGrlFal78gtxGg&oe=680A2B15" alt="D">
          </div>
          <div class="slide">
            <img src="https://scontent.fmnl4-1.fna.fbcdn.net/v/t1.6435-9/59992516_1225481570959264_124940018820055040_n.jpg?_nc_cat=103&ccb=1-7&_nc_sid=cf85f3&_nc_eui2=AeHDwQ7yyxi8kE3lO6rkF_Q67CJ7pdPjVjDsInul0-NWMAi-PKxUrM-l_hkrXRsbSlmjCPNQQPrp9kkOau2xYe3P&_nc_ohc=BXPFAvdyXoMQ7kNvgHEqBcF&_nc_oc=AdnTIuE98Z4jlPx8e3T9lH19rPZmm4yyLaoKEQPT7ItToh_U3UfNg7MYmJPHa_ssutw&_nc_zt=23&_nc_ht=scontent.fmnl4-1.fna&_nc_gid=9y394FjwGZDyRlzxSUX9SQ&oh=00_AYHubXE4LL9vk8GWc_sSRwQHu_lhILQg4sT4XWk6pwgnXg&oe=680A1399" alt="D">
          </div>
        </div>
        
        <div class="indicators">
          <label for="slide1"></label>
          <label for="slide2"></label>
          <label for="slide3"></label>
          <label for="slide4"></label>
        </div>
      </div>
    
      <section class="products-section">
        <div class="section-header">
          <h2>T-SHIRTS</h2>
          <a href="products/tshirts.php" class="view-all">View All</a>
        </div>
    
        <div class="products-grid">
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/SidePocketBlackShirtFront.jpg?v=1742283606" alt="Cipher Tee Black and Neon Green">
              <p>CIPHER TEE 2025 - BLACK AND NEON GREEN<br>₱1,000.00</p>
            </a>
          </div>
    
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/Cipher_Black_and_White_Shirt_Front.jpg?v=1742283658" alt="CIPHER TEE 2025 - BLACK AND WHITE">
              <p>CIPHER TEE 2025 - BLACK AND WHITE<br>₱1,000.00</p>
            </a>
          </div>
    
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/Slant_Cream_Shirt_Front.jpg?v=1742283821" alt="SLANT TEE 2025 - CREAM AND BLACK">
              <p>SLANT TEE 2025 - CREAM AND BLACK<br>₱1,100.00</p>
            </a>
          </div>
    
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/Slant_Cream_Shirt_Back.jpg?v=1742283821" alt="SLANT TEE 2025 - BLACK AND WHITE">
              <p>SLANT TEE 2025 - BLACK AND WHITE<br>₱1,100.00</p>
            </a>
          </div>
        </div>
      </section>

      <section class="products-section">
        <div class="section-header">
          <h2>JACKETS</h2>
          <a href="products/jackets.html" class="view-all">View All</a>
        </div>

        <div class="products-grid">
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHER_STREAK_CREWNECK_1.jpg?v=1728695354" alt="CIPHER STREAK CREWNECK - MID GRAY/ OFF WHITE">
              <p>CIPHER STREAK CREWNECK - MID GRAY/ OFF WHITE<br>₱2,300.00</p>
            </a>
          </div>

          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERSTREAKCREWNECK3.jpg?v=1728695291" alt="CIPHER STREAK CREWNECK - BROWN CREAM">
              <p>CIPHER STREAK CREWNECK - BROWN CREAM<br>₱2,300.00</p>
            </a>
          </div>

          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/D-SPARKPANELEDJACKET5.jpg?v=1728696742" alt="D-SPARK PANELED JACKET - CREAM BEIGE">
              <p>D-SPARK PANELED JACKET - CREAM BEIGE<br>₱3,000.00</p>
            </a>
          </div>

          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/OAKSHADEWORKWEARJACKET1.jpg?v=1737095917" alt="OAKSHADE WORKWEAR JACKET">
              <p>OAKSHADE WORKWEAR JACKET<br>₱2,800.00</p>
            </a>
          </div>
        </div>
      </section>

      <section class="products-section">
        <div class="section-header">
          <h2>PANTS</h2>
          <a href="products/pants.html" class="view-all">View All</a>
        </div>

        <div class="products-grid">
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/OAKSHADEWIDEPANTS1.jpg?v=1737096791" alt="OAKSHADE WIDE PANTS">
              <p>OAKSHADE WIDE PANTS<br>₱2,500.00</p>
            </a>
          </div>

          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/D-SPARKPANELEDPANTS3.jpg?v=1728696742" alt="D-SPARK PANELED PANTS - CREAM BEIGE">
              <p>D-SPARK PANELED PANTS - CREAM BEIGE<br>₱2,700.00</p>
            </a>
          </div>
    
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/RACING_PANTS.jpg?v=1741421203" alt="RACING PANTS">
              <p>RACING PANTS<br>₱5,995.00</p>
            </a>
          </div>
    
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/MERGE_WIDE_PANTS_1.jpg?v=1737099107" alt="MERGE WIDE PANTS - BLACK">
              <p>MERGE WIDE PANTS - BLACK<br>₱2,300.00</p>
            </a>
          </div>
        </div>
      </section>

      <section class="products-section">
        <div class="section-header">
          <h2>SHORTS</h2>
          <a href="products/shorts.html" class="view-all">View All</a>
        </div>

        <div class="products-grid">
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS3.jpg?v=1734574023" alt="CIPHER SPLICED SHORTS - KHAKI/CREAM">
              <p>CIPHER SPLICED SHORTS - KHAKI/CREAM<br>₱1,100.00</p>
            </a>
          </div>
    
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS5.jpg?v=1734573884" alt="CIPHER SPLICED SHORTS - WHITE/GREY">
              <p>CIPHER SPLICED SHORTS - WHITE/GREY<br>₱1,100.00</p>
            </a>
          </div>
    
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/SWIFTSHORTS1.jpg?v=1728698184" alt="SWIFT SHORTS - MULTI TONAL BLACK GRAY">
              <p>SWIFT SHORTS - MULTI TONAL BLACK GRAY<br>₱1,100.00</p>
            </a>
          </div>
    
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS1.jpg?v=1734573884" alt="CIPHER SPLICED SHORTS - BLACK/GRAY">
              <p>CIPHER SPLICED SHORTS - BLACK/GRAY<br>₱1,100.00</p>
            </a>
          </div>
        </div>
      </section>

      <section class="products-section">
        <div class="section-header">
          <h2>ACCESSORIES</h2>
          <a href="products/accessories.html" class="view-all">View All</a>
        </div>

        <div class="products-grid">
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/GRANDPRIXENAMELPIN.png?v=1733378380" alt="GRAND PRIX ENAMEL PIN">
              <p>GRAND PRIX ENAMEL PIN<br>₱300.00</p>
            </a>
          </div>
    
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/HYPERGARAGESTICKERPACK.jpg?v=1732590086" alt="HYPER GARAGE STICKER PACK">
              <p>HYPER GARAGE STICKER PACK<br>₱350.00</p>
            </a>
          </div>
    
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/HYPERGARAGEWOVENTAG1.jpg?v=1732589996" alt="HYPER GARAGE WOVEN KEYCHAIN">
              <p>HYPER GARAGE WOVEN KEYCHAIN<br>₱300.00</p>
            </a>
          </div>
    
          <div class="product-card">
            <a href="#">
              <img src="https://dbtkco.com/cdn/shop/files/HYPERGARAGEMETALKEYCHAIN.jpg?v=1732589925" alt="HYPER GARAGE METAL KEYCHAIN">
              <p>HYPER GARAGE METAL KEYCHAIN<br>₱350.00</p>
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
    </body>
</html>