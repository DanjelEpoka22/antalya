<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Shto stilet për slider -->
<style>
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
    
    .image-slider {
        position: relative;
        height: 250px;
        overflow: hidden;
    }
    
    .slider-container {
        position: relative;
        height: 100%;
    }
    
    .slider-images {
        display: flex;
        transition: transform 0.5s ease;
        height: 100%;
    }
    
    .slider-image {
        min-width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .slider-nav {
        position: absolute;
        bottom: 10px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        gap: 5px;
    }
    
    .slider-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: background 0.3s ease;
    }
    
    .slider-dot.active {
        background: white;
    }
    
    .slider-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.5);
        color: white;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .slider-container:hover .slider-arrow {
        opacity: 1;
    }
    
    .slider-prev {
        left: 10px;
    }
    
    .slider-next {
        right: 10px;
    }
    
    .price-breakdown {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin: 10px 0;
    }
    
    .price-item {
        display: flex;
        justify-content: between;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }
    
    .services-list {
        font-size: 0.85rem;
    }
    
    .services-list li {
        margin-bottom: 3px;
    }
    
    .included-services {
        color: #28a745;
    }
    
    .excluded-services {
        color: #dc3545;
    }
</style>

<div class="container mt-4">
    <h1 class="mb-4">All Tours</h1>
    
    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Tours</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
                           placeholder="Search by title or location...">
                </div>
                <div class="col-md-3">
                    <label for="max_price" class="form-label">Max Price (Adult)</label>
                    <input type="number" class="form-control" id="max_price" name="max_price" 
                           value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>" 
                           placeholder="Maximum price">
                </div>
                <div class="col-md-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" 
                           value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>" 
                           placeholder="Filter by location">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tours Grid -->
    <div class="row">
        <?php
        // Krijimi i query-it bazë
        $sql = "SELECT t.*, 
                       GROUP_CONCAT(ti.image_path ORDER BY ti.sort_order) as images 
                FROM tours t 
                LEFT JOIN tour_images ti ON t.id = ti.tour_id 
                WHERE 1=1";
        
        // Shto filtera nëse ekzistojnë
        if(isset($_GET['search']) && !empty($_GET['search'])) {
            $search = mysqli_real_escape_string($conn, $_GET['search']);
            $sql .= " AND (t.title LIKE '%$search%' OR t.description LIKE '%$search%' OR t.location LIKE '%$search%')";
        }
        
        if(isset($_GET['max_price']) && !empty($_GET['max_price'])) {
            $max_price = mysqli_real_escape_string($conn, $_GET['max_price']);
            $sql .= " AND t.price_adult <= $max_price";
        }
        
        if(isset($_GET['location']) && !empty($_GET['location'])) {
            $location = mysqli_real_escape_string($conn, $_GET['location']);
            $sql .= " AND t.location LIKE '%$location%'";
        }
        
        $sql .= " GROUP BY t.id ORDER BY t.created_at DESC";
        
        $result = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                // Përpunimi i fotove
                $images = [];
                if(!empty($row['images'])) {
                    $images = explode(',', $row['images']);
                }
                // Nëse nuk ka foto, përdor foton bazë
                if(empty($images) && $row['image']) {
                    $images = [$row['image']];
                }
                if(empty($images)) {
                    $images = ['default.jpg'];
                }
                
                // Përpunimi i shërbimeve të përfshira/jo të përfshira
                $included_services = !empty($row['included']) ? explode(',', $row['included']) : [];
                $excluded_services = !empty($row['excluded']) ? explode(',', $row['excluded']) : [];
                
                echo '
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card tour-card h-100">
                        <div class="image-slider">';
                
                // Slider i fotove
                if(count($images) > 0) {
                    echo '<div class="slider-container" id="slider-' . $row['id'] . '">
                            <div class="slider-images">';
                    
                    foreach($images as $index => $image) {
                        echo '<img src="assets/images/tours/' . trim($image) . '" 
                                 class="slider-image" 
                                 alt="' . htmlspecialchars($row['title']) . ' - Image ' . ($index + 1) . '">';
                    }
                    
                    echo '</div>';
                    
                    // Butonat e navigimit vetëm nëse ka më shumë se 1 foto
                    if(count($images) > 1) {
                        echo '<button class="slider-arrow slider-prev" onclick="prevSlide(' . $row['id'] . ')">
                                <i class="fas fa-chevron-left"></i>
                              </button>
                              <button class="slider-arrow slider-next" onclick="nextSlide(' . $row['id'] . ')">
                                <i class="fas fa-chevron-right"></i>
                              </button>
                              <div class="slider-nav">';
                        
                        foreach($images as $index => $image) {
                            echo '<div class="slider-dot ' . ($index === 0 ? 'active' : '') . '" 
                                  onclick="goToSlide(' . $row['id'] . ', ' . $index . ')"></div>';
                        }
                        
                        echo '</div>';
                    }
                    
                    echo '</div>';
                }
                
                echo '</div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">' . htmlspecialchars($row['title']) . '</h5>
                            <p class="card-text flex-grow-1">' . htmlspecialchars($row['description']) . '</p>
                            
                            <!-- Price Breakdown -->
                            <div class="price-breakdown">
                                <div class="price-item">
                                    <span>Adult (12+):</span>
                                    <strong>$' . ($row['price_adult'] ?? $row['price']) . '</strong>
                                </div>
                                <div class="price-item">
                                    <span>Child (4-11):</span>
                                    <strong>$' . ($row['price_child'] ?? round($row['price'] * 0.7, 2)) . '</strong>
                                </div>
                                <div class="price-item">
                                    <span>Infant (0-3):</span>
                                    <strong>$' . ($row['price_infant'] ?? '0') . '</strong>
                                </div>
                            </div>
                            
                            <!-- Services -->
                            <div class="services-list mb-3">';
                
                // Shërbimet e përfshira
                if(!empty($included_services)) {
                    echo '<h6 class="text-success mb-2"><i class="fas fa-check me-1"></i>Included:</h6>
                          <ul class="included-services">';
                    foreach(array_slice($included_services, 0, 3) as $service) {
                        echo '<li>' . htmlspecialchars(trim($service)) . '</li>';
                    }
                    if(count($included_services) > 3) {
                        echo '<li>... and more</li>';
                    }
                    echo '</ul>';
                }
                
                // Shërbimet jo të përfshira
                if(!empty($excluded_services)) {
                    echo '<h6 class="text-danger mb-2 mt-2"><i class="fas fa-times me-1"></i>Not Included:</h6>
                          <ul class="excluded-services">';
                    foreach(array_slice($excluded_services, 0, 3) as $service) {
                        echo '<li>' . htmlspecialchars(trim($service)) . '</li>';
                    }
                    if(count($excluded_services) > 3) {
                        echo '<li>... and more</li>';
                    }
                    echo '</ul>';
                }
                
                echo '</div>
                            
                            <div class="mt-auto">
                                <p class="card-text"><small class="text-muted">Duration: ' . htmlspecialchars($row['duration']) . '</small></p>
                                <p class="card-text"><small class="text-muted">Location: ' . htmlspecialchars($row['location']) . '</small></p>';
                
                if(isset($_SESSION['user_id'])) {
                    echo '<a href="booking.php?tour_id=' . $row['id'] . '" class="btn btn-primary w-100">Book Now</a>';
                } else {
                    echo '<a href="login.php" class="btn btn-outline-primary w-100">Login to Book</a>';
                }
                
                echo '
                            </div>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-info text-center">No tours found matching your criteria.</div></div>';
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- JavaScript për slider -->
<script>
    const sliders = {};
    
    function initSlider(tourId, totalSlides) {
        sliders[tourId] = {
            currentSlide: 0,
            totalSlides: totalSlides
        };
    }
    
    function nextSlide(tourId) {
        if(!sliders[tourId]) return;
        
        const slider = sliders[tourId];
        slider.currentSlide = (slider.currentSlide + 1) % slider.totalSlides;
        updateSlider(tourId);
    }
    
    function prevSlide(tourId) {
        if(!sliders[tourId]) return;
        
        const slider = sliders[tourId];
        slider.currentSlide = (slider.currentSlide - 1 + slider.totalSlides) % slider.totalSlides;
        updateSlider(tourId);
    }
    
    function goToSlide(tourId, slideIndex) {
        if(!sliders[tourId]) return;
        
        sliders[tourId].currentSlide = slideIndex;
        updateSlider(tourId);
    }
    
    function updateSlider(tourId) {
        const slider = sliders[tourId];
        const sliderElement = document.getElementById('slider-' + tourId);
        if(!sliderElement) return;
        
        const images = sliderElement.querySelector('.slider-images');
        const dots = sliderElement.querySelectorAll('.slider-dot');
        
        // Update position
        images.style.transform = `translateX(-${slider.currentSlide * 100}%)`;
        
        // Update dots
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === slider.currentSlide);
        });
    }
    
    // Initialize all sliders on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.slider-container').forEach(container => {
            const tourId = container.id.replace('slider-', '');
            const totalSlides = container.querySelectorAll('.slider-image').length;
            if(totalSlides > 1) {
                initSlider(tourId, totalSlides);
            }
        });
    });
</script>