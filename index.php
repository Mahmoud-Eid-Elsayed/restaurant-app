<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description"
    content="ELCHEF - Experience culinary excellence with our finest dishes and exceptional service">
  <meta name="author" content="ITI Team 4-2025=>(Mahmoud Eid, Ahmed haidar, somaya hassan, Eithar Wageh)">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="src/assets/libraries/fontawesome-6.7.2-web/css/all.min.css">
  <link rel="stylesheet" href="src/assets/libraries/node_modules/bootstrap/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="src/static/css/landingpage/landing-pg.css">
  <title>ELCHEF - Fine Dining Restaurant</title>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img id="logo-Elchef" src="src/assets/images/site-logo/logo.webp" alt="ELCHEF Logo" class="img-fluid">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
        aria-controls="offcanvasNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="offcanvas offcanvas-end" id="offcanvasNavbar">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title">
            <img src="src/assets/images/site-logo/logo.webp" alt="ELCHEF Logo" class="img-fluid" style="height: 40px;">
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="navbar-nav mx-auto mb-3 mb-lg-0">
            <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#chef-res">Our Chefs</a></li>
            <li class="nav-item"><a class="nav-link" href="src/static/php/menu/menu.php">Menu</a></li>
            <li class="nav-item"><a class="nav-link" href="#menu-offers ">Special
                Offers</a></li>
            <li class="nav-item"><a class="nav-link" href="#about-us">About Us</a></li>
            <li class="nav-item"><a class="nav-link" href="src/static/php/table-reservation/book_table.php">Book a
                Table</a></li>
          </ul>

          <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="./src/static/html/user/signup.php">
              <i class="fas fa-user-plus me-2"></i>Sign Up
            </a>
            <a class="btn btn-primary" href="./src/static/html/user/login.php">
              <i class="fas fa-sign-in-alt me-2"></i>Log In
            </a>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active" data-bs-interval="3000">
        <img src="src/assets/images/slider/rest-slider-01.webp" class="d-block w-100" alt="Fine Dining Experience">
        <div class="carousel-caption d-none d-md-block animate-fadeInUp">
          <h1 class="display-4 fw-bold mb-4 animate-fadeInLeft delay-1">Welcome to ELCHEF</h1>
          <p class="lead mb-4 animate-fadeInRight delay-2">Experience culinary excellence in every bite</p>
          <a href="#rsrv-rest" class="btn btn-primary btn-lg animate-fadeInUp delay-3">
            <i class="fas fa-utensils me-2"></i>Reserve Your Table
          </a>
        </div>
      </div>
      <div class="carousel-item" data-bs-interval="3000">
        <img src="src/assets/images/slider/rest-slider-02.webp" class="d-block w-100" alt="Signature Dishes">
        <div class="carousel-caption d-none d-md-block animate-fadeInUp">
          <h1 class="display-4 fw-bold mb-4">Exquisite Cuisine</h1>
          <p class="lead mb-4">Discover our chef's signature creations</p>
          <a href="src/static/php/menu/menu.php" class="btn btn-primary btn-lg">
            <i class="fas fa-book-open me-2"></i>View Menu
          </a>
        </div>
      </div>
      <div class="carousel-item" data-bs-interval="3000">
        <img src="src/assets/images/slider/rest-slider-03.webp" class="d-block w-100" alt="Special Events">
        <div class="carousel-caption d-none d-md-block animate-fadeInUp">
          <h1 class="display-4 fw-bold mb-4">Special Events</h1>
          <p class="lead mb-4">Create memorable moments with us</p>
          <a href="#about-us" class="btn btn-primary btn-lg">
            <i class="fas fa-glass-cheers me-2"></i>Learn More
          </a>
        </div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>

  <section id="chef-res" class="animate-fadeInUp">
    <div class="container">
      <div class="text-center mb-5">
        <h1 class="animate-fadeInUp delay-1">Our Culinary Team</h1>
        <h3 class="animate-fadeInUp delay-2">Meet Our Expert Chefs</h3>
      </div>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card h-100">
            <img src="src/assets/images/chefs/chef-01.webp" class="card-img-top" alt="Ibrahim Omran">
            <div class="card-body text-center">
              <h5 class="card-title">Ibrahim Omran</h5>
              <p class="text-muted mb-3">Master Chef</p>
              <p class="card-text">With over 15 years of experience in international cuisine, Chef Ibrahim brings
                innovation and excellence to every dish.</p>
              <div class="social-links mt-3">
                <a href="#" class="text-primary me-2"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-info me-2"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-danger"><i class="fab fa-instagram"></i></a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100">
            <img src="src/assets/images/chefs/chef-02.webp" class="card-img-top" alt="Bosina Adham">
            <div class="card-body text-center">
              <h5 class="card-title">Bosina Adham</h5>
              <p class="text-muted mb-3">Patissier</p>
              <p class="card-text">Specializing in French pastries and desserts, Chef Bosina creates edible works of art
                that delight both eyes and palate.</p>
              <div class="social-links mt-3">
                <a href="#" class="text-primary me-2"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-info me-2"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-danger"><i class="fab fa-instagram"></i></a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100">
            <img src="src/assets/images/chefs/chef-03.webp" class="card-img-top" alt="Said Gommah">
            <div class="card-body text-center">
              <h5 class="card-title">Said Gommah</h5>
              <p class="text-muted mb-3">Sous Chef</p>
              <p class="card-text">Chef Said's passion for Mediterranean cuisine and attention to detail ensures every
                dish meets our high standards.</p>
              <div class="social-links mt-3">
                <a href="#" class="text-primary me-2"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-info me-2"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-danger"><i class="fab fa-instagram"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Menu Preview Section -->
  <section id="menu-preview" class="animate-fadeInUp">
    <div class="container">
      <div class="text-center mb-5">
        <h1 class="animate-fadeInUp delay-1">Our Menu</h1>
        <p class="lead text-muted animate-fadeInUp delay-2">Experience culinary excellence with our diverse selection
        </p>
      </div>
      <div class="row g-4 justify-content-center">
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-img-wrapper">
              <img src="src/static/uploads/Menu-item/_13f7e066-28ed-416a-b0ef-9b27c3075bc7.jpeg" class="card-img-top"
                alt="Appetizers">
            </div>
            <div class="card-body text-center">
              <h5 class="card-title">Appetizers</h5>
              <p class="card-text">Start your culinary journey with our exquisite appetizers</p>
              <a href="src/static/php/menu/menu.php?category=2" class="btn btn-primary mt-3">
                <i class="fas fa-utensils me-2"></i>View Appetizers
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-img-wrapper">
              <img src="src/static/uploads/Menu-item/453458708_392009743499161_3475548967102124824_n.jpeg"
                class="card-img-top" alt="Main Course">
            </div>
            <div class="card-body text-center">
              <h5 class="card-title">Main Course</h5>
              <p class="card-text">Savor our chef's signature main course creations</p>
              <a href="src/static/php/menu/menu.php?category=2" class="btn btn-primary mt-3">
                <i class="fas fa-drumstick-bite me-2"></i>View Main Courses
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-img-wrapper">
              <img src="src/static/uploads/Menu-item/3hero.jpeg" class="card-img-top" alt="Desserts">
            </div>
            <div class="card-body text-center">
              <h5 class="card-title">Desserts</h5>
              <p class="card-text">Complete your meal with our delightful desserts</p>
              <a href="src/static/php/menu/menu.php?category=3" class="btn btn-primary mt-3">
                <i class="fas fa-ice-cream me-2"></i>View Desserts
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center mt-5">
        <a href="src/static/php/menu/menu.php" class="btn btn-primary btn-lg">
          <i class="fas fa-book-open me-2"></i>View Full Menu
        </a>
      </div>
    </div>
  </section>

  <!-- Special Offers Preview -->
  <section id="menu-offers" class="animate-fadeInUp bg-light">
    <div class="container">
      <div class="text-center mb-5">
        <h1 class="animate-fadeInUp delay-1">Featured Offers</h1>
        <p class="lead text-muted animate-fadeInUp delay-2">Discover our most popular dining experiences</p>
      </div>
      <div class="row g-4">
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-badge position-absolute top-0 end-0 bg-primary text-white m-3 py-2 px-3 rounded-pill">
              MOST POPULAR
            </div>
            <img src="src/assets/images/offers/offer-01.webp" class="card-img-top" alt="Weekend Special">
            <div class="card-body text-center">
              <h5 class="card-title">Weekend Special</h5>
              <p class="card-text">Enjoy 20% off on all menu items every weekend. Perfect for family gatherings!</p>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-badge position-absolute top-0 end-0 bg-primary text-white m-3 py-2 px-3 rounded-pill">
              NEW
            </div>
            <img src="src/assets/images/offers/offer-02.webp" class="card-img-top" alt="Happy Hour">
            <div class="card-body text-center">
              <h5 class="card-title">Happy Hour</h5>
              <p class="card-text">Buy one get one free on all drinks from 5 PM to 7 PM, Monday to Thursday.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center mt-5">
        <a href="src/static/php/menu/specialOrder.php" class="btn btn-primary btn-lg">
          <i class="fas fa-tag me-2"></i>View All Offers
        </a>
      </div>
    </div>
  </section>

  <section id="about-us" class="animate-fadeInUp">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <img src="src/assets/images/about-Us/about-us.webp" class="img-fluid rounded shadow animate-fadeInLeft"
            alt="About ELCHEF">
        </div>
        <div class="col-md-6">
          <h2 class="display-4 mb-4 animate-fadeInRight delay-1">Our Story</h2>
          <p class="lead mb-4 animate-fadeInRight delay-2">Welcome to <strong class="text-primary">ELCHEF</strong>,
            where passion meets culinary excellence.</p>

          <div class="feature-item d-flex align-items-start mb-4">
            <div class="icon-box me-3">
              <i class="fas fa-utensils fa-2x text-primary"></i>
            </div>
            <div>
              <h5 class="mb-2">Culinary Excellence</h5>
              <p class="text-muted">Our expert chefs craft each dish with precision and creativity, using only the
                finest ingredients.</p>
            </div>
          </div>

          <div class="feature-item d-flex align-items-start mb-4">
            <div class="icon-box me-3">
              <i class="fas fa-heart fa-2x text-primary"></i>
            </div>
            <div>
              <h5 class="mb-2">Passionate Service</h5>
              <p class="text-muted">We're dedicated to providing an exceptional dining experience that exceeds
                expectations.</p>
            </div>
          </div>

          <div class="feature-item d-flex align-items-start mb-4">
            <div class="icon-box me-3">
              <i class="fas fa-star fa-2x text-primary"></i>
            </div>
            <div>
              <h5 class="mb-2">Quality Ingredients</h5>
              <p class="text-muted">We source the finest local and international ingredients to ensure superior quality
                in every dish.</p>
            </div>
          </div>

          <a href="#rsrv-rest" class="btn btn-primary btn-lg mt-4">
            <i class="fas fa-calendar-alt me-2"></i>Reserve Your Table
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Reservation Preview -->
  <section class="resr animate-fadeInUp" id="rsrv-rest">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <img src="src/assets/images/reservation/reservation-pic.webp" class="img-fluid animate-fadeInLeft"
            id="reservation-pic" alt="Book a Table">
        </div>
        <div class="col-md-6">
          <div class="reservation-preview p-4 bg-white rounded shadow animate-fadeInRight">
            <h2 class="text-center mb-4">Reserve Your Table</h2>
            <p class="lead text-center mb-4">Experience fine dining at its best. Book your table now and create
              memorable moments with us.</p>
            <div class="features mb-4">
              <div class="feature-item d-flex align-items-center mb-3">
                <i class="fas fa-check-circle text-primary me-3"></i>
                <span>Easy online booking process</span>
              </div>
              <div class="feature-item d-flex align-items-center mb-3">
                <i class="fas fa-check-circle text-primary me-3"></i>
                <span>Instant confirmation</span>
              </div>
              <div class="feature-item d-flex align-items-center mb-3">
                <i class="fas fa-check-circle text-primary me-3"></i>
                <span>Special requests accommodation</span>
              </div>
            </div>
            <div class="text-center">
              <a href="src/static/php/table-reservation/book_table.php" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-calendar-check me-2"></i>Book Your Table Now
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="bg-dark text-light py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4 animate-fadeInLeft delay-1">
          <h5>About ELCHEF</h5>
          <p>Experience the finest dining with our carefully crafted menu, exceptional service, and elegant atmosphere.
          </p>
          <div class="social-links mt-3">
            <a href="#" class="me-2"><i class="fab fa-facebook fa-lg"></i></a>
            <a href="#" class="me-2"><i class="fab fa-twitter fa-lg"></i></a>
            <a href="#" class="me-2"><i class="fab fa-instagram fa-lg"></i></a>
          </div>
        </div>
        <div class="col-md-4 animate-fadeInUp delay-2">
          <h5>Quick Links</h5>
          <ul class="list-unstyled">
            <li><a href="#" class="text-light text-decoration-none mb-2 d-block">Home</a></li>
            <li><a href="#menu-offers" class="text-light text-decoration-none mb-2 d-block">Special Offers</a></li>
            <li><a href="#about-us" class="text-light text-decoration-none mb-2 d-block">About Us</a></li>
            <li><a href="#rsrv-rest" class="text-light text-decoration-none mb-2 d-block">Reservations</a></li>
          </ul>
        </div>
        <div class="col-md-4 animate-fadeInRight delay-3">
          <h5>Contact Us</h5>
          <ul class="list-unstyled">
            <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>10 Ramadan ST, Cairo</li>
            <li class="mb-2"><i class="fas fa-phone me-2"></i>+201234567890</li>
            <li class="mb-2"><i class="fas fa-envelope me-2"></i>El-Chef@gmail.com</li>
          </ul>
        </div>
      </div>
      <hr class="my-4">
      <div class="text-center">
        <p class="mb-0">&copy; 2025 ELCHEF Restaurant. All rights reserved.</p>
        <small class="text-light">Designed with <i class="fas fa-heart text-danger"></i> by ITI Team 4(Mahmoud
          Elsayed,Somaya Hassan,Ahmed Hidar,Eithar Wageh)</small>
      </div>
    </div>
  </footer>

  <script src="src/assets/libraries/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('bookingForm').addEventListener('submit', function (event) {
      event.preventDefault();
      document.getElementById('confirmationMessage').classList.remove('d-none');
      setTimeout(() => {
        document.getElementById('confirmationMessage').classList.add('d-none');
      }, 5000);
    });

    window.addEventListener('scroll', function () {
      if (window.scrollY > 50) {
        document.querySelector('.navbar').classList.add('scrolled');
      } else {
        document.querySelector('.navbar').classList.remove('scrolled');
      }

      // Scroll reveal animation
      const reveals = document.querySelectorAll('.reveal');
      reveals.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;

        if (elementTop < window.innerHeight - elementVisible) {
          element.classList.add('active');
        }
      });
    });

    // Initialize animations on page load
    document.addEventListener('DOMContentLoaded', function () {
      // Add reveal classes to elements
      const animatedElements = document.querySelectorAll('.animate-fadeInUp, .animate-fadeInLeft, .animate-fadeInRight');
      animatedElements.forEach(element => {
        element.classList.add('reveal');

        // Add appropriate reveal direction class
        if (element.classList.contains('animate-fadeInLeft')) {
          element.classList.add('reveal-left');
        } else if (element.classList.contains('animate-fadeInRight')) {
          element.classList.add('reveal-right');
        } else {
          element.classList.add('reveal-bottom');
        }
      });

      // Trigger initial scroll check
      window.dispatchEvent(new Event('scroll'));
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });
  </script>
</body>

</html>