    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h4 class="mb-4">
                        <i class="fas fa-sun me-2"></i>Rreze Antalya
                    </h4>
                    <p class="mb-4">Creating unforgettable travel experiences in the heart of Antalya. Discover the beauty of Turkish Riviera with our carefully curated tours.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5 class="mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-white-50 text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="tours.php" class="text-white-50 text-decoration-none">Tours</a></li>
                        <li class="mb-2"><a href="about.php" class="text-white-50 text-decoration-none">About</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-white-50 text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="mb-4">Support</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="faq.php" class="text-white-50 text-decoration-none">FAQ</a></li>
                        <li class="mb-2"><a href="privacy.php" class="text-white-50 text-decoration-none">Privacy Policy</a></li>
                        <li class="mb-2"><a href="terms.php" class="text-white-50 text-decoration-none">Terms of Service</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-white-50 text-decoration-none">Help Center</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5 class="mb-4">Contact Info</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <span class="text-white-50">Konyaaltı, Antalya, Turkey</span>
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-phone me-2"></i>
                            <span class="text-white-50">+90 242 123 4567</span>
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-envelope me-2"></i>
                            <span class="text-white-50">info@rrezeantalya.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-5 bg-white-50">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Rreze Antalya. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Made with <i class="fas fa-heart text-danger"></i> for travelers</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Navbar scroll effect - SAFE
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (navbar && window.scrollY > 100) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = '0 5px 20px rgba(0,0,0,0.1)';
            } else if (navbar) {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
            }
        });

        // Smooth scrolling - SAFE (only for anchor links)
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Animation on scroll - SAFE
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe elements for animation - SAFE
        document.addEventListener('DOMContentLoaded', function() {
            const animatedElements = document.querySelectorAll('.tour-card, .feature-icon');
            animatedElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });

            // === SAFE FORM VALIDATION - ONLY VISUAL FEEDBACK ===
            
            // Password match validation (visual only)
            const confirmPassword = document.getElementById('confirm_password');
            if (confirmPassword) {
                confirmPassword.addEventListener('input', function() {
                    const password = document.getElementById('password');
                    if (password && this.value && password.value !== this.value) {
                        this.style.borderColor = '#dc3545';
                    } else {
                        this.style.borderColor = '#198754';
                    }
                });
            }

            // Date validation (visual only)
            const bookingDate = document.getElementById('booking_date');
            if (bookingDate) {
                bookingDate.addEventListener('change', function() {
                    const selectedDate = new Date(this.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (selectedDate < today) {
                        alert('Please select a future date.');
                        this.value = '';
                    }
                });
            }

            // Price calculation (visual only)
            const guestsSelect = document.getElementById('guests');
            if (guestsSelect) {
                guestsSelect.addEventListener('change', function() {
                    const guests = parseInt(this.value);
                    const pricePerPerson = parseFloat(this.getAttribute('data-price')) || 0;
                    const totalPriceElement = document.getElementById('total_price');
                    if (totalPriceElement) {
                        totalPriceElement.textContent = (guests * pricePerPerson).toFixed(2);
                    }
                });
            }
        });

        // === SAFE LOADING STATES - NO FORM SUBMISSION BLOCKING ===
        
        // Visual loading states without preventing form submission
        document.addEventListener('submit', function(e) {
            const form = e.target;
            
            // Only add visual loading for forms that explicitly want it
            if (form.classList.contains('with-loading')) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                    
                    // Store original text for potential revert
                    submitBtn.setAttribute('data-original-text', originalText);
                    
                    // Revert after 10 seconds if page hasn't changed (safety net)
                    setTimeout(() => {
                        if (submitBtn.getAttribute('data-original-text')) {
                            submitBtn.innerHTML = submitBtn.getAttribute('data-original-text');
                        }
                    }, 10000);
                }
            }
        });
    </script>
</body>
</html>