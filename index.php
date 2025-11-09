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
                <p class="lead mb-4">Experience unforgettable tours and reliable private transfers in one of Turkey's most beautiful coastal cities.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="tours.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-compass me-2"></i>Explore Tours
                    </a>
                    <a href="transport.php" class="btn btn-success btn-lg">
                        <i class="fas fa-car me-2"></i>Book Transfer
                    </a>
                </div>
                <div class="mt-5">
                    <div class="row text-center">
                        <div class="col-3">
                            <h3 class="fw-bold">50+</h3>
                            <p class="mb-0">Amazing Tours</p>
                        </div>
                        <div class="col-3">
                            <h3 class="fw-bold">2K+</h3>
                            <p class="mb-0">Happy Travelers</p>
                        </div>
                        <div class="col-3">
                            <h3 class="fw-bold">98%</h3>
                            <p class="mb-0">Satisfaction Rate</p>
                        </div>
                        <div class="col-3">
                            <h3 class="fw-bold">24/7</h3>
                            <p class="mb-0">Transfer Service</p>
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
            <div class="col-md-3 text-center">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4>Safe & Secure</h4>
                <p class="text-muted">Your safety is our top priority with licensed guides and insured activities.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="feature-icon">
                    <i class="fas fa-tag"></i>
                </div>
                <h4>Best Prices</h4>
                <p class="text-muted">Competitive pricing without compromising on quality and experience.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h4>24/7 Support</h4>
                <p class="text-muted">Round-the-clock customer support to assist you throughout your journey.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="feature-icon">
                    <i class="fas fa-car"></i>
                </div>
                <h4>Private Transfers</h4>
                <p class="text-muted">Comfortable private transfers from airport to all major tourist areas.</p>
            </div>
        </div>
    </div>
</section>

<!-- Transport Services Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Private Transfer Services</h2>
                <p class="lead mb-4">Comfortable and reliable private transfers from Antalya Airport to all major tourist destinations.</p>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-warning me-3 fa-lg"></i>
                            <span>Mercedes Vito (up to 6 passengers)</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-warning me-3 fa-lg"></i>
                            <span>Sprinter (larger groups)</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-warning me-3 fa-lg"></i>
                            <span>24/7 Service</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-warning me-3 fa-lg"></i>
                            <span>English Speaking Drivers</span>
                        </div>
                    </div>
                </div>
                <a href="transport.php" class="btn btn-light btn-lg">
                    <i class="fas fa-car me-2"></i>Book Your Transfer
                </a>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4">
                        <h5 class="card-title text-dark mb-4">Transfer Prices</h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-primary mb-2">Belek</h6>
                                    <h4 class="fw-bold text-dark">€30</h4>
                                    <small class="text-muted">Each way</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-primary mb-2">Kemer</h6>
                                    <h4 class="fw-bold text-dark">€50</h4>
                                    <small class="text-muted">Each way</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-primary mb-2">Side</h6>
                                    <h4 class="fw-bold text-dark">€65</h4>
                                    <small class="text-muted">Each way</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-primary mb-2">Alanya</h6>
                                    <h4 class="fw-bold text-dark">€90</h4>
                                    <small class="text-muted">Each way</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
            $sql = "SELECT t.*, 
                           GROUP_CONCAT(ti.image_path ORDER BY ti.sort_order) as images 
                    FROM tours t 
                    LEFT JOIN tour_images ti ON t.id = ti.tour_id 
                    GROUP BY t.id 
                    ORDER BY t.created_at DESC 
                    LIMIT 3";
            $result = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    // Përpunimi i fotove
                    $images = [];
                    if(!empty($row['images'])) {
                        $images = explode(',', $row['images']);
                    }
                    // Nëse nuk ka foto të shumta, përdor foton bazë
                    if(empty($images) && $row['image']) {
                        $images = [$row['image']];
                    }
                    if(empty($images)) {
                        $images = ['default.jpg'];
                    }
                    
                    // Përpunimi i shërbimeve
                    $included_services = !empty($row['included']) ? explode(',', $row['included']) : [];
                    
                    echo '
                    <div class="col-lg-4 col-md-6">
                        <div class="card tour-card h-100">
                            <div class="position-relative">';
                    
                    // Image Slider për fotot e shumta
                    if(count($images) > 0) {
                        echo '<div class="image-slider-small">
                                <div class="slider-container-small" id="slider-' . $row['id'] . '">
                                    <div class="slider-images-small">';
                        
                        foreach($images as $index => $image) {
                            echo '<img src="assets/images/tours/' . trim($image) . '" 
                                     class="slider-image-small" 
                                     alt="' . htmlspecialchars($row['title']) . ' - Image ' . ($index + 1) . '">';
                        }
                        
                        echo '</div>';
                        
                        // Butonat e navigimit vetëm nëse ka më shumë se 1 foto
                        if(count($images) > 1) {
                            echo '<div class="slider-nav-small">';
                            foreach($images as $index => $image) {
                                echo '<div class="slider-dot-small ' . ($index === 0 ? 'active' : '') . '"></div>';
                            }
                            echo '</div>';
                        }
                        
                        echo '</div>
                              </div>';
                    }
                    
                    echo '      <div class="price-tag">
                                    From $' . ($row['price_adult'] ?? $row['price']) . '
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">' . htmlspecialchars($row['title']) . '</h5>
                                <p class="card-text text-muted flex-grow-1">' . htmlspecialchars(substr($row['description'], 0, 120)) . '...</p>
                                
                                <!-- Price Breakdown -->
                                <div class="price-breakdown-small mb-3">
                                    <div class="d-flex justify-content-between small">
                                        <span>Adult:</span>
                                        <strong>$' . ($row['price_adult'] ?? $row['price']) . '</strong>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span>Child:</span>
                                        <strong>$' . ($row['price_child'] ?? round($row['price'] * 0.7, 2)) . '</strong>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span>Infant:</span>
                                        <strong>$' . ($row['price_infant'] ?? '0') . '</strong>
                                    </div>
                                </div>
                                
                                <!-- Included Services Preview -->
                                ' . (!empty($included_services) ? '
                                <div class="included-preview mb-3">
                                    <small class="text-success fw-bold">
                                        <i class="fas fa-check me-1"></i>Includes:
                                    </small>
                                    <small class="text-muted">' . htmlspecialchars(trim($included_services[0])) . 
                                    (count($included_services) > 1 ? ' + ' . (count($included_services) - 1) . ' more' : '') . '</small>
                                </div>' : '') . '
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>' . htmlspecialchars($row['duration']) . '
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>' . htmlspecialchars($row['location']) . '
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

<style>
    /* Styles for image slider in featured tours */
    .image-slider-small {
        position: relative;
        height: 250px;
        overflow: hidden;
    }
    
    .slider-container-small {
        position: relative;
        height: 100%;
    }
    
    .slider-images-small {
        display: flex;
        transition: transform 0.5s ease;
        height: 100%;
    }
    
    .slider-image-small {
        min-width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .slider-nav-small {
        position: absolute;
        bottom: 10px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        gap: 4px;
    }
    
    .slider-dot-small {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        transition: background 0.3s ease;
        cursor: pointer;
    }
    
    .slider-dot-small.active {
        background: white;
    }
    
    .price-breakdown-small {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 10px;
        font-size: 0.85rem;
    }
    
    .included-preview {
        border-left: 3px solid #28a745;
        padding-left: 10px;
    }
    
    .price-tag {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 8px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        z-index: 10;
    }
    
    .tour-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .tour-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .tour-card:hover .slider-images-small {
        transform: scale(1.05);
        transition: transform 0.5s ease;
    }
</style>

<script>
    // Auto-slide functionality for featured tours
    document.addEventListener('DOMContentLoaded', function() {
        const sliders = document.querySelectorAll('.slider-container-small');
        
        sliders.forEach((slider, index) => {
            const images = slider.querySelector('.slider-images-small');
            const dots = slider.querySelectorAll('.slider-dot-small');
            const totalSlides = dots.length;
            
            if (totalSlides > 1) {
                let currentSlide = 0;
                
                // Auto-advance slides
                setInterval(() => {
                    currentSlide = (currentSlide + 1) % totalSlides;
                    images.style.transform = `translateX(-${currentSlide * 100}%)`;
                    
                    dots.forEach((dot, dotIndex) => {
                        dot.classList.toggle('active', dotIndex === currentSlide);
                    });
                }, 4000);
                
                // Click on dots to navigate
                dots.forEach((dot, dotIndex) => {
                    dot.addEventListener('click', () => {
                        currentSlide = dotIndex;
                        images.style.transform = `translateX(-${currentSlide * 100}%)`;
                        
                        dots.forEach((d, i) => {
                            d.classList.toggle('active', i === currentSlide);
                        });
                    });
                });
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>