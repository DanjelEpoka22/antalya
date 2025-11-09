<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<style>
    .transport-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 4px solid #28a745;
    }
    
    .transport-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    
    .price-badge {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 1.2rem;
    }
    
    .vehicle-card {
        text-align: center;
        padding: 30px 20px;
        border-radius: 15px;
        transition: all 0.3s ease;
    }
    
    .vehicle-card:hover {
        transform: scale(1.05);
    }
    
    .vehicle-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #28a745;
    }
</style>

<div class="container mt-5 pt-4">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="fw-bold text-dark mb-3">Private Transfer Services</h1>
            <p class="lead text-muted">Comfortable and reliable private transfers from Antalya Airport to all major tourist destinations</p>
        </div>
    </div>

    <!-- Vehicle Options -->
    <div class="row mb-5">
        <div class="col-md-6 mb-4">
            <div class="vehicle-card bg-light">
                <i class="fas fa-shuttle-van vehicle-icon"></i>
                <h4 class="fw-bold mb-3">Mercedes Vito</h4>
                <p class="text-muted mb-3">Perfect for families and small groups</p>
                <div class="features">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-users text-success me-3"></i>
                        <span>Up to 6 passengers</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-suitcase text-success me-3"></i>
                        <span>Ample luggage space</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-air-freshener text-success me-3"></i>
                        <span>Air conditioned</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="vehicle-card bg-light">
                <i class="fas fa-bus vehicle-icon"></i>
                <h4 class="fw-bold mb-3">Mercedes Sprinter</h4>
                <p class="text-muted mb-3">Ideal for larger groups</p>
                <div class="features">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-users text-success me-3"></i>
                        <span>Up to 15 passengers</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-suitcase text-success me-3"></i>
                        <span>Large luggage capacity</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-phone text-success me-3"></i>
                        <span>Contact for pricing</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Zones & Prices -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="text-center mb-4 fw-bold">Transfer Prices</h3>
            <div class="row g-4">
                <?php
                // Merr zonat e transportit nga databaza
                $zones_sql = "SELECT * FROM transport_zones ORDER BY price ASC";
                $zones_result = mysqli_query($conn, $zones_sql);
                
                if(mysqli_num_rows($zones_result) > 0) {
                    while($zone = mysqli_fetch_assoc($zones_result)) {
                        echo '
                        <div class="col-lg-3 col-md-6">
                            <div class="transport-card h-100">
                                <div class="card-body text-center p-4">
                                    <h5 class="card-title fw-bold text-dark mb-3">' . htmlspecialchars($zone['name']) . '</h5>
                                    <div class="price-badge mb-3">
                                        €' . $zone['price'] . '
                                    </div>
                                    <p class="text-muted small mb-4">' . htmlspecialchars($zone['description']) . '</p>';
                        
                        if(isset($_SESSION['user_id'])) {
                            echo '<a href="booking_transport.php?zone_id=' . $zone['id'] . '" class="btn btn-success w-100">
                                    <i class="fas fa-car me-2"></i>Book Transfer
                                  </a>';
                        } else {
                            echo '<a href="login.php" class="btn btn-outline-success w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to Book
                                  </a>';
                        }
                        
                        echo '
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    // Nëse nuk ka zona në databazë, shfaq manualisht
                    $default_zones = [
                        ['name' => 'Airport → Belek', 'price' => 30, 'desc' => 'Private transfer from Antalya Airport to Belek area'],
                        ['name' => 'Belek → Airport', 'price' => 30, 'desc' => 'Private transfer from Belek area to Antalya Airport'],
                        ['name' => 'Airport → Kemer', 'price' => 50, 'desc' => 'Private transfer from Antalya Airport to Kemer area'],
                        ['name' => 'Kemer → Airport', 'price' => 50, 'desc' => 'Private transfer from Kemer area to Antalya Airport'],
                        ['name' => 'Airport → Side', 'price' => 65, 'desc' => 'Private transfer from Antalya Airport to Side area'],
                        ['name' => 'Side → Airport', 'price' => 65, 'desc' => 'Private transfer from Side area to Antalya Airport'],
                        ['name' => 'Airport → Alanya', 'price' => 90, 'desc' => 'Private transfer from Antalya Airport to Alanya area'],
                        ['name' => 'Alanya → Airport', 'price' => 90, 'desc' => 'Private transfer from Alanya area to Antalya Airport']
                    ];
                    
                    foreach($default_zones as $zone) {
                        echo '
                        <div class="col-lg-3 col-md-6">
                            <div class="transport-card h-100">
                                <div class="card-body text-center p-4">
                                    <h5 class="card-title fw-bold text-dark mb-3">' . $zone['name'] . '</h5>
                                    <div class="price-badge mb-3">
                                        €' . $zone['price'] . '
                                    </div>
                                    <p class="text-muted small mb-4">' . $zone['desc'] . '</p>';
                        
                        if(isset($_SESSION['user_id'])) {
                            echo '<button onclick="showTransportModal(\'' . $zone['name'] . '\', ' . $zone['price'] . ')" class="btn btn-success w-100">
                                    <i class="fas fa-car me-2"></i>Book Transfer
                                  </button>';
                        } else {
                            echo '<a href="login.php" class="btn btn-outline-success w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to Book
                                  </a>';
                        }
                        
                        echo '
                                </div>
                            </div>
                        </div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h4 class="text-center mb-4 fw-bold">Why Choose Our Transfer Service?</h4>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-clock text-success me-3 mt-1 fa-lg"></i>
                                <div>
                                    <h6 class="fw-bold">24/7 Service</h6>
                                    <p class="text-muted mb-0">We operate round the clock to meet your flight schedules</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-user-tie text-success me-3 mt-1 fa-lg"></i>
                                <div>
                                    <h6 class="fw-bold">Professional Drivers</h6>
                                    <p class="text-muted mb-0">English-speaking, licensed, and experienced drivers</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-shield-alt text-success me-3 mt-1 fa-lg"></i>
                                <div>
                                    <h6 class="fw-bold">Safe & Insured</h6>
                                    <p class="text-muted mb-0">All vehicles are fully insured and regularly maintained</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-comments text-success me-3 mt-1 fa-lg"></i>
                                <div>
                                    <h6 class="fw-bold">Free Consultation</h6>
                                    <p class="text-muted mb-0">Contact us for custom transfer solutions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-5">
                        <div class="alert alert-info">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-info-circle me-2"></i>For Sprinter Transfers
                            </h6>
                            <p class="mb-2">Contact us directly for larger groups requiring Sprinter vehicles</p>
                            <a href="tel:+1234567890" class="btn btn-primary btn-sm me-2">
                                <i class="fas fa-phone me-1"></i>Call: +1 234 567 890
                            </a>
                            <a href="https://wa.me/1234567890" class="btn btn-success btn-sm" target="_blank">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transport Modal (for manual booking if database not set up) -->
<div class="modal fade" id="transportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Book Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>You are booking: <strong id="modalRoute"></strong></p>
                <p>Price: <strong id="modalPrice"></strong></p>
                <p>Please contact us to complete your booking:</p>
                <div class="d-grid gap-2">
                    <a href="tel:+1234567890" class="btn btn-primary">
                        <i class="fas fa-phone me-2"></i>Call Now
                    </a>
                    <a href="https://wa.me/1234567890" class="btn btn-success" target="_blank">
                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTransportModal(route, price) {
    document.getElementById('modalRoute').textContent = route;
    document.getElementById('modalPrice').textContent = '€' + price;
    var modal = new bootstrap.Modal(document.getElementById('transportModal'));
    modal.show();
}
</script>

<?php include 'includes/footer.php'; ?>