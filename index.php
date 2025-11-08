<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section with Video Background -->
<section class="hero-section">
    <!-- Video Background -->
    <div class="video-background">
        <!-- Zëvendëso video-n lokale me YouTube embed -->
        <div class="youtube-video-bg">
            <iframe 
                src="https://www.youtube.com/embed/OK4h4OfBOVM?autoplay=1&mute=1&loop=1&playlist=OK4h4OfBOVM&controls=0&showinfo=0&rel=0&modestbranding=1&background=1" 
                frameborder="0" 
                allow="autoplay; encrypted-media" 
                allowfullscreen>
            </iframe>
        </div>
        <div class="video-overlay"></div>
    </div>
    
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content fade-in-up">
                <h1 class="display-3 fw-bold mb-4">Discover The Magic of Antalya</h1>
                <p class="lead mb-4">Experience unforgettable tours in one of Turkey's most beautiful coastal cities. From ancient ruins to stunning beaches, we create memories that last a lifetime.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="tours.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-compass me-2"></i>Explore Tours
                    </a>
                    <a href="#featured-tours" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-play me-2"></i>Watch Story
                    </a>
                </div>
                <div class="mt-5">
                    <div class="row text-center">
                        <div class="col-4">
                            <h3 class="fw-bold">50+</h3>
                            <p class="mb-0">Amazing Tours</p>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold">2K+</h3>
                            <p class="mb-0">Happy Travelers</p>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold">98%</h3>
                            <p class="mb-0">Satisfaction Rate</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <!-- Optional: Add a play button or additional content on the right side -->
                <div class="video-cta">
                    <i class="fas fa-play-circle fa-6x text-white opacity-75 mb-3"></i>
                    <p class="text-white">Experience Antalya in 4K</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4>Safe & Secure</h4>
                <p class="text-muted">Your safety is our top priority with licensed guides and insured activities.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="feature-icon">
                    <i class="fas fa-tag"></i>
                </div>
                <h4>Best Prices</h4>
                <p class="text-muted">Competitive pricing without compromising on quality and experience.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h4>24/7 Support</h4>
                <p class="text-muted">Round-the-clock customer support to assist you throughout your journey.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Tours Section -->
<section id="featured-tours" class="py-5">
    <div class="container">
        <h2 class="text-center section-title">Featured Tours</h2>
        <div class="row g-4">
            <?php
            $sql = "SELECT * FROM tours ORDER BY created_at DESC LIMIT 3";
            $result = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo '
                    <div class="col-lg-4 col-md-6">
                        <div class="card tour-card h-100">
                            <div class="position-relative">
                                <img src="assets/images/tours/' . ($row['image'] ? $row['image'] : 'default.jpg') . '" 
                                     class="card-img-top" alt="' . $row['title'] . '">
                                <div class="price-tag">
                                    $' . $row['price'] . '
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">' . $row['title'] . '</h5>
                                <p class="card-text text-muted flex-grow-1">' . substr($row['description'], 0, 120) . '...</p>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>' . $row['duration'] . '
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>' . $row['location'] . '
                                        </small>
                                    </div>';
                    
                    if(isset($_SESSION['user_id'])) {
                        echo '<a href="booking.php?tour_id=' . $row['id'] . '" class="btn btn-primary w-100">
                                <i class="fas fa-calendar-plus me-2"></i>Book Now
                              </a>';
                    } else {
                        echo '<a href="login.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Book
                              </a>';
                    }
                    
                    echo '
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<div class="col-12 text-center">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No tours available at the moment.
                        </div>
                      </div>';
            }
            ?>
        </div>
        <div class="text-center mt-5">
            <a href="tours.php" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-eye me-2"></i>View All Tours
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center section-title">What Our Travelers Say</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-quote-left text-primary fa-2x"></i>
                        </div>
                        <p class="card-text">"The Antalya City Tour was absolutely breathtaking! Our guide was knowledgeable and made the experience unforgettable."</p>
                        <div class="mt-3">
                            <h6 class="mb-1">Sarah Johnson</h6>
                            <small class="text-muted">From USA</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-quote-left text-primary fa-2x"></i>
                        </div>
                        <p class="card-text">"Pamukkale Hot Springs exceeded all expectations. The natural beauty combined with historical sites was incredible."</p>
                        <div class="mt-3">
                            <h6 class="mb-1">Michael Chen</h6>
                            <small class="text-muted">From Canada</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-quote-left text-primary fa-2x"></i>
                        </div>
                        <p class="card-text">"Excellent service from start to finish. The Mediterranean Cruise was the highlight of our Turkey vacation!"</p>
                        <div class="mt-3">
                            <h6 class="mb-1">Emma Rodriguez</h6>
                            <small class="text-muted">From Spain</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5" style="background: linear-gradient(135deg, var(--primary), var(--accent)); color: white;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h3 class="fw-bold mb-2">Stay Updated</h3>
                <p class="mb-0">Subscribe to our newsletter for exclusive deals and travel tips.</p>
            </div>
            <div class="col-lg-6">
                <div class="input-group">
                    <input type="email" class="form-control" placeholder="Enter your email">
                    <button class="btn btn-light" type="button">
                        <i class="fas fa-paper-plane me-2"></i>Subscribe
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>